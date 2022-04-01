<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use App\Entity\Canciones;
use App\Entity\Generos;
use App\Entity\Tono;
use App\Form\DatosCancionesType;
use App\Repository\CancionesRepository;
use ContainerScy3PtY\getCancionesService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PersistirDatosController extends AbstractController
{
    /**
     * @Route("/PersistirDatos", name="persistir")
     */
    public function PersistirDatos(Request $request): Response
    {        
        $canciones = new Canciones();
        // $tonos2 = new Tono("Jejejejeje");
        // $canciones->setTonos(500);
        $form=$this->createForm(DatosCancionesType::class,$canciones);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($canciones);
            $entityManager->flush();
            $canciones = $form->getData();

            return $this->redirectToRoute('persistir');
        }
  
        return $this->render('persistir_datos/index.html.twig', [
            'controller_name' => 'PersistirDatosController',
            'form' =>$form->createView(), 
        ]);
    }
     /**
     * @Route("/Busquedas/{generoCancion}", name="busquedas")
     */

     public function Busquedas($generoCancion){
         $em = $this->getDoctrine()->getManager();
         $canciones = $em->getRepository(Canciones::class)->find(80);
         $canciones2 = $em->getRepository(Canciones::class)->findOneBy(['tonalidad'=>'G','tematica'=>'Consagración']);
         $canciones3 = $em->getRepository(Canciones::class)->findBy(['genero'=>'Rock','tonalidad'=>'Bm']);
         $canciones4 = $em->getRepository(Canciones::class)->findAll();
         $cancionesRepository = $em->getRepository(Canciones::class)->BuscarCancion($generoCancion);


         return $this->render('busquedas/busquedas.html.twig', 
         array('find'=>$canciones,
         'findOneBy'=>$canciones2,
          'findBy'=>$canciones3,
          'findAll'=>$canciones4,
          'BuscarCancion'=>$cancionesRepository
        ));
 
     }


     /**
     * @Route("/buscador{nombre}", name="buscadores")
     */
    public function Buscador(Request $request, $nombre)
    {        
        $form1 = $this->createFormBuilder()
        ->add('genero')
        ->add('Enviar',SubmitType::class)
        ->getForm();

            return $this->render('buscador/buscador.html.twig',
            array("parametro1"=>$nombre,
            'form1'=>$form1->createView()));
        
    }


    /**
     * @Route("/paginador", name="paginador")
     */

    public function index(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Canciones::class)->BuscarTodasLasCanciones();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
     
        return $this->render('paginador/paginador.html.twig',[
            'pagination'=> $pagination
        ]
      );

    }
     /**
     * @Route("/AdicionDatos", name="adiciondatos")
     */



    public function AdicionDatos(): Response
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $cocoslocos = $entityManager->getRepository(Generos::class)->findBy([], ['id'=>'ASC']);
        $datoloco = "reloquisimo";
        
       return $this->render('AdicionarDatos/AdicionDatos.html.twig', [
            'cocoslocos' => $cocoslocos,
            'datoloco' => $datoloco,
            
        ]);

    }






    /**
     * @Route("/HacerAdicionDatos", name="haceradiciondatos")
     */
    public function HacerAdicionarDatos(Request $request): Response
    {       
        // $all_params = $request->request->all();
        // var_dump($all_params);
      
        $ncancion = $request->get("ncancion");
        $tonalidad = $request->get("tonalidad");
        $tempo = $request->get("tempo");
        $tematica = $request->get("tematica");
        $letra = $request->get("letra");
        $genero = $request->get("genero");
        // $generos = $request->get("generos");
        // $em = $this->getDoctrine()->getManager();
        // $getGeneroId= intval($generos);
        // $queryGenero = $em->getRepository(Generos::class)->find(1);
        // dd();
        // dd(gettype($getGeneroId), $getGeneroId);
        // $generoRepositorio = $em->getRepository(Canciones::class);
        // $queryRepositorio = $generoRepositorio->findBy(['id' =>$generos]);
        // dd($ncancion,$tonalidad,$tempo,$tematica,$letra,$genero,$generos);
        $enviarCanciones = new Canciones();
        $enviarCanciones->setNombreCancion($ncancion);
        $enviarCanciones->setTematica($tematica);
        $enviarCanciones->setTonalidad($tonalidad);
        $enviarCanciones->setTempo($tempo);
        $enviarCanciones->setGenero($genero);
        $enviarCanciones->setLetra($letra);

        // $enviarCanciones->setGeneros($getGeneroId);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($enviarCanciones);
        $entityManager->flush();
      
     
            // $enviargeneros = new Canciones();
            // $generosEnteros = (int)$generos;
            // $enviargeneros->setGeneros($queryGenero->getId());
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($enviargeneros);
            
        // $enviarCanciones->setTono($tono);

     

        return $this->render('AdicionarDatos/HacerAdicionDatos.html.twig');
    //    return new JsonResponse([
    //        'generos' => $generos,
    //         'nombre_cancion' => $ncancion,
    //         'tono_id' => $tono,
    //         'tonalidad' => $tonalidad,
    //         'tempo' => $tempo,
    //         'tematica' => $tematica,
    //         'letra' => $letra,
    //         'all_params' => $all_params,
       
    //    ]);
}
     /**
     * @Route("/modificar/{id}", name="modificar")
     */

    public function modificarCanciones($id){
         $em = $this->getDoctrine()->getManager();
         $canciones = $em->getRepository(Canciones::class)->find($id);
             

        return $this->render('modificar/modificar.html.twig', 
        array('find'=>$canciones,

       ));

    }
        /**
     * @Route("/datosmodificados", name="datosmodificados")
     */
    public function DatosModificados(Request $request): Response
    {       
        // $em = $this->getDoctrine()->getRepository(Canciones::class)->find($id);
        // $form = $this->createForm(Canciones::class, $em);
        // $form->handleRequest($request);
        // if ($form->isSubmitted()&&$form->isValid()){
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($em);
        //     $entityManager->flush();
        // }
        
        $ncancion = $request->get("ncancion");
        $tonalidad = $request->get("tonalidad");
        $tempo = $request->get("tempo");
        $tematica = $request->get("tematica");
        $letra = $request->get("letra");
        $genero = $request->get("genero");
        $enviarCanciones = new Canciones();
        $enviarCanciones->setNombreCancion($ncancion);
        $enviarCanciones->setTematica($tematica);
        $enviarCanciones->setTonalidad($tonalidad);
        $enviarCanciones->setTempo($tempo);
        $enviarCanciones->setGenero($genero);
        $enviarCanciones->setLetra($letra);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($enviarCanciones);
        $entityManager->flush();
       

        return $this->render('modificar/datosmodificados.html.twig');

}
   /**
     * @Route("/eliminar/{id}", name="eliminarDatos")
     */
    public function eliminarDatos($id): Response
    {   
        $data = $this->getDoctrine()->getRepository(Canciones::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();  
        $entityManager->remove($data);
        $entityManager->flush();
    
        if ($entityManager){
            $this->addFlash(
                'notice',
                'Tus cambios se han guardado!'
            );

        return $this->redirectToRoute('paginador');
      
        }
}
}
