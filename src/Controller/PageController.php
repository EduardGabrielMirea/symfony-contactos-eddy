<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Provincia;
use App\Entity\User;
use App\Form\ClienteFormType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(): Response{
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/cliente/insertar/{nombre}/{telefono}/{coche}', name: 'insertar_cliente')]
    public function insertar(string $nombre, string $telefono,string $coche, ManagerRegistry $doctrine ): Response{
      $entityManager = $doctrine->getManager();
      $cliente = new Cliente();
      $cliente->setNombre($nombre);
      $cliente->setTelefono($telefono);
      $cliente->setCoche($coche);
      $entityManager->persist($cliente);
        try {
            $entityManager->flush();
            return new Response("Cliente guardado");
        }catch ( \Exception $e ) {
            return new Response("No creado");
        }
    }
    #[Route('/cliente/buscar/{id}', name: 'buscar_cliente')]
    public function buscar(int $id, ManagerRegistry $doctrine ): Response{
        $repository = $doctrine->getRepository(Cliente::class);
        $cliente = $repository->find($id);

        /*
         * dump($clientes);
        exit();
         * */
        return $this->render('ficha_cliente.html.twig', ['cliente' => $cliente]);

    }
    #[Route('/cliente/listar', name: 'listar_clientes')]
    public function listarClientes(ManagerRegistry $doctrine ): Response{
        $repository = $doctrine->getRepository(Cliente::class);
        $clientes = $repository->findAll();

        /*
         * dump($clientes);
        exit();
         * */
        return $this->render('lista_clientes.html.twig', ['clientes' => $clientes]);
    }

    #[Route('/cliente/update/{id}/{nombre}/{telefono}/{coche}', name: 'actualizar_cliente')]
    public function actualizar(int $id, string $nombre, string $telefono, string $coche ,Cliente $cliente, ManagerRegistry $doctrine ): Response{
        $entityManager = $doctrine->getManager();
        $cliente->setNombre($nombre);
        $cliente->setTelefono($telefono);
        $cliente->setCoche($coche);
        $entityManager->persist($cliente);
        return $this->render('ficha_cliente.html.twig', ['cliente' => $cliente]);
    }
    #[Route('/cliente/delete/{id}', name: 'eliminar_cliente')]
    public function delete(int $id, Cliente $cliente, ManagerRegistry $doctrine ): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Cliente::class);
        $cliente = $repositorio->find($id);
        if ($cliente) {
            try {
                $entityManager->remove($cliente);
                $entityManager->flush();
                return new Response("Cliente eliminado");
            }catch ( \Exception $e ) {
                return new Response("Cliente no encontrado");
            }
        }else{
            return $this ->render('ficha_cliente.html.twig', ['cliente' => null]);
        }
    }

    #[Route('/cliente/insertarconprovincia', name: 'insertar_con_provincia_cliente')]
    public function insertarConProvincia(ManagerRegistry $doctrine ): Response{
        $entityManager = $doctrine->getManager();
        $cliente = new Cliente();

        $provincia = new Provincia();
        $provincia->setNombre("Castellón");

        $cliente->setNombre("Juan");
        $cliente->setCoche("audi");
        $cliente->setTelefono("222222");
        $cliente->setProvincia($provincia);
        $entityManager->persist($provincia);
        $entityManager->persist($cliente);
        $entityManager->flush();
        return $this->render('ficha_cliente.html.twig', ['cliente' => $cliente]);
    }

    #[Route('/cliente/insertarsinnprovincia', name: 'insertar_sin_provincia_cliente')]
    public function insertarSinProvincia(ManagerRegistry $doctrine ): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Provincia::class);
        /*
         * Solo puedes insertar a provincias que ya existan. Si no, tienes que crear primero la provincia.
         * */
        $provincia = $repositorio->findOneBy(["nombre"=>"Castellón"]);

        $cliente = new Cliente();
        $cliente->setNombre("Pepe");
        $cliente->setTelefono("333333");
        $cliente->setCoche("seat");
        $cliente->setProvincia($provincia);
        $entityManager->persist($cliente);
        $entityManager->flush();
        return $this->render('ficha_cliente.html.twig', ['cliente' => $cliente]);
    }

    #[Route('/cliente/nuevo', name: 'crear_cliente')]
    public function contact(ManagerRegistry $doctrine, Request $request): Response
    {
        $cliente = new Cliente();
        $form = $this->createForm(ClienteFormType::class, $cliente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cliente = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($cliente);
            $entityManager->flush();
            return $this->redirectToRoute('listar_clientes', [ 'id' => $cliente->getId() ]);
        }
        return $this->render('page/cliente.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/cliente/editar/{id}', name: 'editar_cliente')]
    public function contactEdith(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $cliente = $entityManager->find(Cliente::class, $id);
        $form = $this->createForm(ClienteFormType::class, $cliente);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cliente = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($cliente);
            $entityManager->flush();
            return $this->redirectToRoute('listar_clientes', [ 'id' => $cliente->getId() ]);
        }
        return $this->render('page/cliente.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
