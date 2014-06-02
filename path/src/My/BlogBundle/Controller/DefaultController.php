<?php

namespace My\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use My\BlogBundle\Entity\Article;
use My\BlogBundle\Form\ArticleType;


class DefaultController extends Controller
{
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$articles = $em->getRepository('MyBlogBundle:Article')->findAll();
        
        return $this->render('MyBlogBundle:Default:index.html.twig', array(
        	'articles' => $articles));
    }

    public function ajouterAction(){

    	$em = $this->getDoctrine()->getEntityManager();

    	$a = new Article();
    	$form = $this->createForm(new ArticleType(), $a);

    	$request = $this->getRequest();
    	if($request->isMethod('POST')){
    		
    		$form->bind($request);

    		if($form->isValid()){
	    		$a = $form->getData();
	    		$em->persist($a);
	    		$em->flush();

	    		return $this->redirect($this->generateUrl('blog_homepage'));
    			
    		}
    		

    	}

    	return $this->render('MyBlogBundle:Default:ajouter.html.twig', array(
    		'form' => $form->createView(),
    		));
    }

    public function editerAction(Article $article){

    	$em = $this->getDoctrine()->getEntityManager();

    	$form = $this->createForm(new ArticleType(), $article);

    	$request = $this->getRequest();
    	if($request->isMethod('POST')){
    		
    		$form->bind($request);

    		if($form->isValid()){
	    		$a = $form->getData();
	    		$em->persist($a);
	    		$em->flush();

	    		return $this->redirect(
	    			$this->generateUrl('blog_voir', array(
	    				'id'=>$a->getId(),
	    			))
	    		);
    			
    		}
    		

    	}

    	return $this->render('MyBlogBundle:Default:editer.html.twig', array(
    		'form' => $form->createView(),  
    		'article' => $article  		
    		));
    }

    public function voirAction(Article $article){

    	return $this->render('MyBlogBundle:Default:voir.html.twig', array(
    		'article' => $article
    		));
    }

    public function supprimerAction(Article $article){

    	$em = $this->getDoctrine()->getEntityManager();

    	$em->remove($article);
    	$em->flush();

    	return $this->redirect($this->generateUrl('blog_homepage'));
    }
}
