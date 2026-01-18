<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\services\ReportService;
use yii\filters\AccessControl;
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

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['top-authors'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'top-authors' => ['GET', 'POST'],
                ],
            ],
        ];
    }

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
