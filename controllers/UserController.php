<?php

declare(strict_types=1);

namespace app\controllers;

use app\dto\UserSignupDto;
use app\models\City;
use app\repositories\CityRepositoryInterface;
use app\repositories\UserRepositoryInterface;
use app\requests\UserSignupRequest;
use app\services\UserService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property mixed $taskRepository
 */
class UserController extends Controller
{
    public function __construct(
        mixed $id,
        mixed $module,
        private readonly UserRepositoryInterface $userRepository,
        private readonly CityRepositoryInterface $cityRepository,
        private readonly UserService $userService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * Displays a single User model.
     *
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('Исполнитель не найден.');
        }

        $canViewContacts = !$user->executorProfile?->hide_my_contacts;

        if (!$canViewContacts && !Yii::$app->user->isGuest) {
            $canViewContacts = $this->taskRepository->hasActiveTaskWithExecutor(
                (int) Yii::$app->user->id,
                $user->id
            );
        }

        return $this->render('view', [
            'user' => $user,
            'canViewContacts' => $canViewContacts,
        ]);
    }

    public function actionSignup()
    {
        $signupForm = new UserSignupRequest();

        if ($this->request->isPost) {
            $signupForm->load($this->request->post());

            if ($signupForm->validate()) {
                $signupDto = new UserSignupDto(
                    name: $signupForm->name,
                    email: $signupForm->email,
                    cityId: (int) $signupForm->cityId,
                    password: $signupForm->password,
                    isExecutor: (bool) $signupForm->isExecutor,
                );

                $user = $this->userService->signup($signupDto);

                Yii::$app->user->login($user);

                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $signupForm,
            'cities' => $this->cityRepository->findAllForSelect(),
        ]);
    }
}
