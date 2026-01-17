<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\exceptions\NotFoundException;
use app\services\SubscriptionService;
use app\components\AccessHelper;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly SubscriptionService $subscriptionService,
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
            'access' => AccessHelper::guestOnly(['subscribe']),
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'subscribe' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param int $authorId
     * @return Response
     * @throws NotFoundException
     */
    public function actionSubscribe(int $authorId): Response
    {
        try {
            $this->subscriptionService->subscribe($authorId, Yii::$app->request->post());
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления о новых книгах автора.');
        } catch (NotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['author/view', 'id' => $authorId]);
    }
}
