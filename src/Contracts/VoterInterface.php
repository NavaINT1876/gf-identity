<?php

namespace NavaINT1876\GfIdentity\Contracts;

use app\modules\v1\voters\UserVoter;
use yii\web\ForbiddenHttpException;

/**
 * Interface VoterInterface
 * Stands for decision making point whether to grant access to user or not.
 * It is possible to add some logic and conditions to class which implements this interface to
 * check for additional requirements.
 *
 * Instance can be passed to NavaINT1876\GfIdentity\EvaGuard::denyAccessUnlessGranted() as a second argument,
 * and $params for it as a third argument.
 *
 * Example of usage:
 *
 * ```php
 *
 *  public function checkAccess($action, $model = null, $params = [])
 *  {
 *      if ('index' === $action) {
 *          self::denyAccessUnlessGranted('ROLE_ADMIN');
 *      }
 *
 *      if ('view' === $action || 'update' === $action) {
 *          self::denyAccessUnlessGranted('ROLE_USER', UserVoter::class, ['model' => $model, 'action' => $action]);
 *      }
 *  }
 *
 * ```
 *
 * Example of voter:
 * ```php
 *
 *  namespace app\modules\v1\voters;
 *
 *  use Yii;
 *  use yii\web\ForbiddenHttpException;
 *
 *  class UserVoter implements VoterInterface
 *  {
 *      const ACTION_VIEW = 'view';
 *
 *      const ACTION_EDIT = 'update';
 *
 *      const ACTIONS = [
 *          self::ACTION_VIEW,
 *          self::ACTION_EDIT,
 *      ];
 *
 *      private $params;
 *
 *      public function __construct(array $params)
 *      {
 *          $this->params = $params;
 *      }
 *
 *      public function decide(): void
 *      {
 *          $action = $this->params['action'];
 *          $subject = $this->params['model'];
 *          $currentUser = Yii::$app->identity->getUser();
 *
 *          if ($currentUser->id === $subject->id && in_array($action, self::ACTIONS)) {
 *              return;
 *          }
 *
 *          throw new ForbiddenHttpException('You do not have permission to view/edit this user details.');
 *      }
 * }
 *
 * ```
 *
 * @package NavaINT1876\GfIdentity\Contracts
 */
interface VoterInterface
{
    /**
     * Make decision whether to grant access or not.
     * If decision was not to grant access throws ForbiddenHttpException.
     *
     * For decision uses current User fetched from JWT token `Yii::$app->identity->getUser()` and
     * $params array variable where one could pass parameters for additional conditions and decision making.
     *
     * @return void
     * @throws ForbiddenHttpException
     */
    public function decide(): void;
}
