<?php
/**
 * Clase de Autenticacón por BD
 * Copiado de core/libs/auth2/adapters/model_auth.php
 * Ésta clase se creó debido a que Auth2 no permite usar password_hash.
 */
class MyAuthModel extends MyAuth
{

    /**
     * Modelo a utilizar para el proceso de autenticacion
     *
     * @var String
     */
    protected $_model = 'users';
    /**
     * Namespace de sesion donde se cargaran los campos del modelo
     *
     * @var string
     */
    protected $_sessionNamespace = 'default';
    /**
     * Campos que se cargan del modelo
     *
     * @var array
     */
    protected $_fields = array('id');
     /**
     *
     *
     * @var string
     */
    protected $_algos ;
     /**
     *
     *
     * @var string
     */
    protected $_key;
    /**
     * Asigna el modelo a utilizar
     *
     * @param string $model nombre de modelo
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * Asigna el namespace de sesion donde se cargaran los campos de modelo
     *
     * @param string $namespace namespace de sesion
     */
    public function setSessionNamespace($namespace)
    {
        $this->_sessionNamespace = $namespace;
    }

    /**
     * Indica que campos del modelo se cargaran en sesion
     *
     * @param array $fields campos a cargar
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * Check
     *
     * @param $username
     * @param $password
     * @return bool
     */
    protected function _check($username, $password)
    {
        // TODO $_SERVER['HTTP_HOST'] puede ser una variable por si quieren ofrecer autenticacion desde cualquier host indicado
        if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === FALSE) {
            self::log('INTENTO HACK IP ' . $_SERVER['HTTP_REFERER']);
            $this->setError('Intento de Hack!');
            return FALSE;
        }
        
        //$username = addslashes($username);
        $username = filter_var($username, FILTER_SANITIZE_MAGIC_QUOTES);

        $Model = new $this->_model;
        if ($user = $Model->find_first("$this->_login = '$username'")) {
            if (password_verify($password, $user->{$this->_pass})) {
                // Carga los atributos indicados en sesion
                foreach ($this->_fields as $field) {
                    Session::set($field, $user->$field, $this->_sessionNamespace);
                }

                Session::set($this->_key, TRUE);
                return TRUE;
            }
        }

        $this->setError('Error Login!');
        Session::set($this->_key, FALSE);
        return FALSE;
    }

    

}
