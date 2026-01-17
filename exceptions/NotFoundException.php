<?php

declare(strict_types=1);

namespace app\exceptions;

use yii\web\NotFoundHttpException as YiiNotFoundHttpException;

class NotFoundException extends YiiNotFoundHttpException
{
}
