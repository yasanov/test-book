<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\exceptions\NotFoundException;
use app\models\Author;
use app\services\AuthorService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class AuthorController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly AuthorService $authorService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete', 'list'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'list'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['createAuthor'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['updateAuthor'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['deleteAuthor'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $dataProvider = $this->authorService->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionList(): Response
    {
        $dataProvider = $this->authorService->getDataProvider(50);

        return $this->asJson([
            'items' => array_map(function ($author) {
                return [
                    'id' => $author->id,
                    'full_name' => $author->full_name,
                ];
            }, $dataProvider->getModels()),
            'pagination' => [
                'page' => $dataProvider->pagination->page + 1,
                'pageSize' => $dataProvider->pagination->pageSize,
                'totalCount' => $dataProvider->totalCount,
                'pageCount' => $dataProvider->pagination->getPageCount(),
            ],
        ]);
    }

    public function actionView(int $id): string
    {
        $model = $this->authorService->getById($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(): string|Response
    {
        $model = new Author();
        $model->loadDefaultValues();

        if (Yii::$app->request->post()) {
            try {
                $author = $this->authorService->create(Yii::$app->request->post());
                Yii::$app->session->setFlash('success', 'Автор успешно создан.');

                return $this->redirect(['view', 'id' => $author->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                $model->load(Yii::$app->request->post());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id): string|Response
    {
        $author = $this->authorService->getById($id);

        if ($author->load(Yii::$app->request->post())) {
            try {
                $author = $this->authorService->update($id, Yii::$app->request->post());
                Yii::$app->session->setFlash('success', 'Автор успешно обновлен.');

                return $this->redirect(['view', 'id' => $author->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $author,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->authorService->delete($id);
            Yii::$app->session->setFlash('success', 'Автор успешно удален.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
