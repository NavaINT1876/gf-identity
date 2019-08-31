<?php

namespace app\modules\v1\services;

use Yii;
use yii\base\InvalidCallException;
use yii\web\ForbiddenHttpException;

trait EvaGuard
{
    /**
     * @param array|string $requiredRoles
     * @throws ForbiddenHttpException
     */
    public static function denyAccessUnlessGranted($requiredRoles)
    {
        if (empty($requiredRoles)) {
            throw new InvalidCallException('At least one role should be provided.');
        }

        $user  = Yii::$app->identity->getUser();

        if (in_array('ROLE_ADMIN', $user->roles)) {
            return;
        }

        $roles = [];
        if (is_string($requiredRoles)) {
            $roles[] = $requiredRoles;
        }

        foreach ($roles as $requiredRole) {
            if (in_array($requiredRole, $user->roles)) {
                return;
            }
        }

        throw new ForbiddenHttpException('You don\'t have permissions to perform this action');
    }
}
