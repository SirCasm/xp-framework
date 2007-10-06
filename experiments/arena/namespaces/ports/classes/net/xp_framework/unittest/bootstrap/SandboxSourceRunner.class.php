<?php
/* This class is part of the XP framework
 *
 * $Id: SandboxSourceRunner.class.php 9284 2007-01-15 18:08:12Z kiesel $ 
 */

  namespace net::xp_framework::unittest::bootstrap;

  ::uses('lang.Process');

  /**
   * Sandbox source code runner
   *
   * @see      xp://lang.Process
   * @purpose  Runs sourcecode in a sandbox
   */
  class SandboxSourceRunner extends lang::Object {
    public
      $executable   = '',
      $settings     = array(),
      $source       = '',
      $stdout       = array(),
      $stderr       = array(),
      $exitcode     = '';

    /**
     * Constructor
     *
     * @throws  lang.IllegalStateException if sapi does not support forking
     */
    public function __construct() {
      if (!isset($_SERVER['_'])) {
        throw(new lang::IllegalStateException('Sandbox not supported in sapi '.php_sapi_name()));
      }
      
      $this->setExecutable(preg_replace('#^/cygdrive/(\w)/#', '$1:/', $_SERVER['_']));
      $this->setSetting('include_path', ini_get('include_path'));
    }

    /**
     * Set Executable
     *
     * @param   string executable
     */
    public function setExecutable($executable) {
      $this->executable= $executable;
    }

    /**
     * Get Executable
     *
     * @return  string
     */
    public function getExecutable() {
      return $this->executable;
    }

    /**
     * Set Settings
     *
     * @param   string key
     * @param   string value
     */
    public function setSetting($key, $value) {
      $this->settings[$key]= $value;
    }
    
    /**
     * Retrieve a single setting
     *
     * @param   string key
     * @return  string
     */
    public function getSetting($key) {
      return $this->settings[$key];
    }    

    /**
     * Get Settings
     *
     * @return  mixed[]
     */
    public function getSettings() {
      return $this->settings;
    }

    /**
     * Set Source
     *
     * @param   string source
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get Source
     *
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

    /**
     * Set Stdout
     *
     * @param   string stdout
     */
    public function setStdout($stdout) {
      $this->stdout= $stdout;
    }

    /**
     * Get Stdout
     *
     * @return  string
     */
    public function getStdout() {
      return $this->stdout;
    }

    /**
     * Set Stderr
     *
     * @param   string stderr
     */
    public function setStderr($stderr) {
      $this->stderr= $stderr;
    }

    /**
     * Get Stderr
     *
     * @return  string
     */
    public function getStderr() {
      return $this->stderr;
    }

    /**
     * Set Exitcode
     *
     * @param   string exitcode
     */
    public function setExitcode($exitcode) {
      $this->exitcode= $exitcode;
    }

    /**
     * Get Exitcode
     *
     * @return  string
     */
    public function getExitcode() {
      return $this->exitcode;
    }

    /**
     * Run the sourcecode in a sandbox
     *
     * @param   string source
     * @return  int exitcode
     */
    public function run($source) {
      $cmdline= $this->getExecutable();
      foreach ($this->settings as $key => $value) {
        $cmdline.= sprintf(' -d%s=%s', $key, $value);
      }

      $p= new lang::Process($cmdline);
      $p->in->write('<?php '.$source.'?>');
      $p->in->close();

      while (!$p->out->eof()) {
        $l= trim($p->out->readLine());
        if (!empty($l)) $this->stdout[]= $l;
      }

      while (!$p->err->eof()) {
        $l= trim($p->err->readLine());
        if (!empty($l)) $this->stderr[]= $l;
      }

      $p->close();
      
      return $this->exitcode= $p->exitValue();
    }
  }
?>