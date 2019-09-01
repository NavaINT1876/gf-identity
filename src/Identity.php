<?php

namespace NavaINT1876\GfIdentity;

use Lcobucci\JWT\Token;
use Yii;
use yii\base\Component;
use yii\web\UnauthorizedHttpException;
use NavaINT1876\GfIdentity\User;

class Identity extends Component
{
    /**
     * @return User
     * @throws UnauthorizedHttpException
     */
    public function getUser(): User
    {
        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');

        if (!$authHeader) {
            throw new UnauthorizedHttpException();
        }

        $authHeader = str_replace('Bearer ', '', $authHeader);

        /** @var Token $token */
        $token = Yii::$app->jwt->getParser()->parse($authHeader);

        $user = new User();
        foreach ($user as $fieldName => $field) {
            $user->{$fieldName} = $token->getClaim($fieldName);
        }

        return $user;
    }
}