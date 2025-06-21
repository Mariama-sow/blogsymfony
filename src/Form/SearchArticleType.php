<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', SearchType::class, [
                'required' => false,
                'label' => 'Recherche',
                'attr' => ['placeholder' => 'Mot-clé...']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'label' => 'Catégorie',
                'placeholder' => 'Toutes catégories'
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Ajouter un tag']
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'required' => false,
                'label' => 'Tags',
                // Optionnel : pour styliser le conteneur
                'attr' => [
                    'class' => 'tags-collection',
                    'data-index' => count($builder->getData() ? $builder->getData()->getTags() ?? [] : [])
                ]
            ]);
    }
}
