<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // src/Repository/ArticleRepository.php

    public function search(?string $query = null, ?Category $category = null, ?array $tags = []): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isPublished = true')
            ->orderBy('a.createdAt', 'DESC');

        if ($query) {
            $qb->andWhere('a.title LIKE :query OR a.content LIKE :query')
            ->setParameter('query', '%'.$query.'%');
        }

        if ($category) {
            $qb->andWhere('a.category = :category')
            ->setParameter('category', $category);
        }

        if (!empty($tags)) {
            $qb->andWhere('JSON_OVERLAPS(a.tags, :tags) = 1')
            ->setParameter('tags', json_encode($tags));
        }

        return $qb->getQuery()->getResult();
    }
}
