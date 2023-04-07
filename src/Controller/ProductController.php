<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

# Cela permet de préfixer toutes nos URL de ce Controller. Très utile pour le contrôle des accès (voir security.yaml)
#[Route('/admin')]
class ProductController extends AbstractController
{
    #[Route('/ajouter-un-produit', name: "create_product", methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $repository, SluggerInterface $slugger): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $product->setCreatedAt(new DateTime());
            $product->setUpdatedAt(new DateTime());

            # On variabilise le fichier de la photo en récupérant les données du formulaire (input photo).
            # On obtient un objet de type UploadedFile()
            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($product, $photo, $slugger);
            } // end if($photo)

            $repository->save($product, true);

            $this->addFlash('success', "Le produit est en ligne avec succès !");
            return $this->redirectToRoute('show_dashboard');
        } // end if($form)

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView()
        ]);
    } // end createProduct()

    #[Route('/modifier-un-produit/{id}', name: 'update_product', methods: ['GET', 'POST'])]
    public function updateProduct(Product $product, Request $request, ProductRepository $repository, SluggerInterface $slugger): Response
    {
        # Récupération de la photo actuelle
        $currentPhoto = $product->getPhoto();

//        dd($request);

        $form = $this->createForm(ProductFormType::class, $product, [
            'photo' => $currentPhoto
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setUpdatedAt(new DateTime());
            $newPhoto = $form->get('photo')->getData();

            if($newPhoto) {
                $this->handleFile($product, $newPhoto, $slugger);
            } else {
                # Si pas de nouvelle photo, alors on resset la photo courante (actuelle).
                $product->setPhoto($currentPhoto);
            }// end if($newPhoto)

            $repository->save($product, true);

            $this->addFlash('success', "La modification a bien été enregistré.");
            return $this->redirectToRoute('show_dashboard');
        } // end if($form)

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    } // end updateProduct()

    #[Route('/archiver-un-produit/{id}', name: 'soft_delete_product', methods: ['GET'])]
    public function softDeleteProduct(Product $product, ProductRepository $repository): Response
    {
        $product->setDeletedAt(new DateTime());

        $repository->save($product, true);

        $this->addFlash('success', "Le produit a bien été archivé");
        return $this->redirectToRoute('show_dashboard');
    }


    /////////////////////////////////////////////// PRIVATE FUNCTIONS ///////////////////////////////////////////
    private function handleFile(Product $product, UploadedFile $photo, SluggerInterface $slugger)
    {
        # 1 - Déconstruire le nom du fichier
        # a : Variabiliser l'extension du fichier : l'extension est DÉDUITE à partir du MIME type du fichier.
        $extension = '.' . $photo->guessExtension();

        # 2 - Assainir le nom du fichier (c-a-d retirer les accents et les espaces blancs)
        $safeFilename = $slugger->slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));

        # 3 - Rendre le nom du fichier unique
        # a - Reconstruire le nom du fichier
        $newFilename = $safeFilename . '_' . uniqid("", true) . $extension;

        # 4 - Déplacer le fichier (upload dans notre application Symfony)
        # On utilise un try/catch lorsqu'une méthode lance (throw) une Exception (erreur)
        try {
            # On a défini un paramètre dans config/service.yaml qui est le chemin (absolu) du dossier 'uploads'
            # On récupère la valeur (le paramètre) avec getParameter() et le nom du param défini dans le fichier service.yaml.
            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            # Si tout s'est bien passé (aucune Exception lancée) alors on doit set le nom de la photo en BDD
            $product->setPhoto($newFilename);
        }
        catch(FileException $exception) {
            $this->addFlash('warning', "Le fichier photo ne s'est pas importé correctement. Veuillez réessayer." . $exception->getMessage());
        } // end catch()
    } // end handleFile()

} // end class