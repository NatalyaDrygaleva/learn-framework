<?php

class controllerUsers extends controller
{
    public function actionRoles()
    {
        $model = $this->getModel('roles');
        $roles = $model->getRoles();
        echo $this->renderLayout([
            'error' => '',
            'modal' => '',
            'content' => $this->renderTemplate('roles_list', [
                'roles' => $roles
            ])
        ]);
    }

    public function actionRoleEdit()
    {
        $model = $this->getModel('roles');
        $role = $model->getRoles((int) (core::app()->input->get['id'] ?? 0));

        $role['privileges'] = $model->getPrivileges();
        
        $role['checked_privileges'] = $model->getPrivileges((int) (core::app()->input->get['id'] ?? 0));
        //echo '<pre>';print_r($role);die();
        
        echo $this->renderLayout([
            'error' => '',
            'modal' => '',
            'content' => $this->renderTemplate('role_edit', $role)
        ]);
    }
    
    public function actionLogout()
    {
        core::app()->user->logout();
        header('Location: /users/login');
    }
    
    public function actionCheckAuth()
    {
        echo '<pre>';
        print_r($_COOKIE);
        print_r($_SESSION);
        echo '</pre>';
        if (core::app()->user->isUser) {
            echo core::app()->user->email . '<br />';
            echo '<a href="/users/logout">Exit</a>';
        } else {
            echo '<a href="/users/login">Enter</a>';
        }
    }

    public function actionLogin()
    {
        $data = core::app()->input->post;
        $data['token'] = core::app()->input->post['token'] ?? core::app()->input->get['token'] ?? '';

        $error = '';
        $modal = '';
        if (core::app()->input->form) {
            try {
                core::app()->user->login($data);
                header('Location: /users/checkAuth');
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        echo $this->renderLayout([
            'error' => $error,
            'modal' => $modal,
            'content' => $this->renderTemplate('login', $data)
        ]);
    }

    public function actionRegistration()
    {
        $error = '';
        $modal = '';
        if (core::app()->input->form) {
            try {
                core::app()->user->registration(core::app()->input->post);
                $modal = $this->renderTemplate('regModal', [
                    'email' => core::app()->input->post['email']
                ]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        echo $this->renderLayout([
            'error' => $error,
            'modal' => $modal,
            'content' => $this->renderTemplate('registration', core::app()->input->post)
        ]);
    }
    
    public function actionForgot()
    {
        $error = '';
        $modal = '';
        if (core::app()->input->form) {
            try {
                core::app()->user->forgot(core::app()->input->post['email']);
                $modal = $this->renderTemplate('resetModal', [
                    'email' => core::app()->input->post['email']
                ]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        echo $this->renderLayout([
            'error' => $error,
            'modal' => $modal,
            'content' => $this->renderTemplate('forgot')
        ]);
    }
    
    public function actionReset()
    {
        $token = core::app()->input->get['token'] ?? core::app()->input->post['token'];
        $error = '';
        $modal = '';
        if (core::app()->input->form) {
            try {
                $data = core::app()->input->post;
                $data['token'] = $token;
                core::app()->user->reset($data);
                $modal = $this->renderTemplate('resetModal', [
                    'email' => core::app()->input->post['email']
                ]);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        echo $this->renderLayout([
            'error' => $error,
            'modal' => $modal,
            'content' => $this->renderTemplate('reset', ['token' => $token])
        ]);
    }
}