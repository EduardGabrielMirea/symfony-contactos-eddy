<?php

namespace App\Controller;

use App\Entity\Cliente;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/cliente/insertar/{nombre}/{telefono}/{coche}', name: 'insertar_cliente')]
    public function insertar(string $nombre, string $telefono,string $coche, ManagerRegistry $doctrine ): Response
    {
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
    public function buscar(int $id, ManagerRegistry $doctrine ): Response
    {
        $repository = $doctrine->getRepository(Cliente::class);
        $cliente = $repository->find($id);

        /*
         * dump($clientes);
        exit();
         * */
        return $this->render('ficha_cliente.html.twig', ['cliente' => $cliente]);

    }
    #[Route('/cliente/listar', name: 'listar_clientes')]
    public function listarClientes(ManagerRegistry $doctrine ): Response
    {
        $repository = $doctrine->getRepository(Cliente::class);
        $clientes = $repository->findAll();

        /*
         * dump($clientes);
        exit();
         * */
        return $this->render('lista_clientes.html.twig', ['clientes' => $clientes]);
    }

    #[Route('/cliente/update/{id}/{nombre}', name: 'actualizar_cliente')]
    public function actualizar(int $id, string $nombre, string $telefono, string $coche ,Cliente $cliente, ManagerRegistry $doctrine ): Response
    {
        $entityManager = $doctrine->getManager();
        $cliente->setNombre($nombre);
        $cliente->setTelefono($telefono);
        $cliente->setCoche($coche);
        $entityManager->persist($cliente);
        return $this->render('lista_clientes.html.twig', ['cliente' => $cliente]);
    }


}
