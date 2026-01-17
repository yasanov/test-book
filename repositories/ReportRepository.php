<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\Query;

class ReportRepository
{
    /**
     * @param int $year
     * @return array
     */
    public function getTopAuthorsByYear(int $year): array
    {
        return (new Query())
            ->select([
                'a.id',
                'a.full_name',
                'COUNT(ba.book_id) as books_count',
            ])
            ->from('{{%authors}} a')
            ->innerJoin('{{%book_author}} ba', 'a.id = ba.author_id')
            ->innerJoin('{{%books}} b', 'ba.book_id = b.id')
            ->where(['b.year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->all();
    }
}
