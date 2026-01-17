<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\exceptions\NotFoundException;
use app\models\Book;
use app\services\BookService;
use app\components\AccessHelper;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class BookController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly BookService $bookService,
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
            'access' => AccessHelper::crudAccess('book', ['index', 'view', 'create', 'update', 'delete']),
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = $this->bookService->getDataProvider();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundException
     */
    public function actionView(int $id): string
    {
        $model = $this->bookService->getById($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate(): string|Response
    {
        $model = new Book();
        $model->loadDefaultValues();

        if (Yii::$app->request->post()) {
            try {
                $coverImageFile = UploadedFile::getInstance($model, 'coverImageFile');
                $authorIds = Yii::$app->request->post('author_ids', []);
                
                $book = $this->bookService->create(
                    Yii::$app->request->post(),
                    $authorIds,
                    $coverImageFile
                );
                
                Yii::$app->session->setFlash('success', 'Книга успешно создана.');

                return $this->redirect(['view', 'id' => $book->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                $model->load(Yii::$app->request->post());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundException
     */
    public function actionUpdate(int $id): string|Response
    {
        $book = $this->bookService->getById($id);

        if (Yii::$app->request->post()) {
            try {
                $coverImageFile = UploadedFile::getInstance($book, 'coverImageFile');
                $authorIds = Yii::$app->request->post('author_ids', []);
                
                $book = $this->bookService->update(
                    $id,
                    Yii::$app->request->post(),
                    $authorIds,
                    $coverImageFile
                );
                
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена.');

                return $this->redirect(['view', 'id' => $book->id]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $selectedAuthorIds = $this->bookService->getSelectedAuthorIds($book);

        return $this->render('update', [
            'model' => $book,
            'selectedAuthorIds' => $selectedAuthorIds,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->bookService->delete($id);
            Yii::$app->session->setFlash('success', 'Книга успешно удалена.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['index']);
    }
}
