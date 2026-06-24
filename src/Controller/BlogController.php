<?php
namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/blog/{id}', name: 'app_blog_article')]
    public function article(int $id, EntityManagerInterface $em): Response
    {
        $article = $em->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Incrémenter les vues
        $article->setVue($article->getVue() + 1);
        $em->flush();

        return $this->render('blog/article.html.twig', [
            'article' => $article
        ]);
    }
}
