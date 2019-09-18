<?php

namespace NavaINT1876\GfIdentity;

use NavaINT1876\GfIdentity\Contracts\VoterInterface;
use Yii;
use yii\base\InvalidCallException;
use yii\web\ForbiddenHttpException;

/**
 * Trait for checking access by roles and possibly by additional custom logic provided in instance of VoterInterface
 *
 * Trait EvaGuard
 * @package NavaINT1876\GfIdentity
 */
trait EvaGuard
{
    /**
     * @param array|string $requiredRoles
     * @param string|null  $voter
     * @param array        $voterParams
     *
     * @throws ForbiddenHttpException
     */
    public static function denyAccessUnlessGranted($requiredRoles, string $voter = null, array $voterParams = [])
    {
        if (empty($requiredRoles)) {
            throw new InvalidCallException('At least one role should be provided.');
        }

        $user = Yii::$app->identity->getUser();

        if (in_array('ROLE_ADMIN', $user->roles)) {
            return;
        }

        $roles = [];
        if (is_string($requiredRoles)) {
            $roles[] = $requiredRoles;
        }

        foreach ($roles as $requiredRole) {
            if (!in_array($requiredRole, $user->roles)) {
                continue;
            }

            if (null !== $voter) {
                self::evaluateVoterRequirements($voter, $voterParams);
            }

            return;
        }

        throw new ForbiddenHttpException('You don\'t have permissions to perform this action');
    }

    /**
     * If Voter is provided then it's method decide() with custom logic and parameters will be run.
     *
     * If access is not granted method decide() will throw ForbiddenHttpException otherwise just return.
     *
     * $voter parameter stands for fully qualified namespace of the according voter class with custom logic in decide() method.
     * For example: "app\modules\v1\voters\UserVoter"
     *
     * $voterParams is array of additional parameters needed to make decision by decide() method. This argument is passed
     * to constructor and can be accessed within a class using `$this->params` expression.
     *
     * @param string|VoterInterface $voter
     * @param array                 $voterParams
     *
     * @throws ForbiddenHttpException
     */
    private static function evaluateVoterRequirements(string $voter, array $voterParams)
    {
        if (!class_exists($voter)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" does not exist', $voter));
        }

        $voterInstance = new $voter($voterParams);

        if (!$voterInstance instanceof VoterInterface) {
            throw new \InvalidArgumentException(\sprintf('Voter "%s" should implement VoterInterface.', $voter));
        }

        $voterInstance->decide();
    }
}
