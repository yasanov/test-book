<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\services\ReportService;
use app\components\AccessHelper;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ReportController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly ReportService $reportService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => AccessHelper::withPermissions([
                'top-authors' => 'viewReport',
            ]),
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'top-authors' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionTopAuthors(): string
    {
        $year = (int)(Yii::$app->request->get('year') ?? date('Y'));
        $topAuthors = $this->reportService->getTopAuthorsByYear($year);

        return $this->render('top-authors', [
            'topAuthors' => $topAuthors,
            'selectedYear' => $year,
        ]);
    }
}
