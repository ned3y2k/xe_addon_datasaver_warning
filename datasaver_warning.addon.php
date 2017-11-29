<?php
/**
 * User: Kyeongdae
 * Date: 2017-06-01
 * Time: 오전 12:14
 */

if (!defined("__XE__")) exit();

if (!class_exists('datasaver_warningAddon')) {
	/**
	 * Class datasaver_warningAddon
	 * @see http://www.xeschool.com/xe/xenote_operation_sequence
	 */
	class datasaver_warningAddon {
		protected $menu;
		private   $addon_path;
		private   $addon_info;

		/** @return datasaver_warningAddon */
		public static function getInstance() {
			static $instance = null;

			if ($instance == null)
				$instance = new self();

			return $instance;
		}

		private function __construct() {
			Context::loadLang(_XE_PATH_ . 'addons/datasaver_warning/lang');
		}

		private function isRequestedDatasaver() {
            $headers =  getallheaders();

            foreach($headers as $key=>$val){
                if(strtolower($key) == 'save-data' && $val == 'on'){
                    return true;
                }
            }

            return false;
		}

		/** @param string $addon_path $addon_path 호출된 애드온의 경로를 담고 있습니다. */
		public function setPath($addon_path) { $this->addon_path = $addon_path; }

		public function getPeriod() {
			if(!isset($this->addon_info->period) || !trim($this->addon_info->period))
				return 3;

			return floatval($this->addon_info->period);
		}

		/**
		 * @param string $addon_info XE의 애드온들은 각각 독자적인 설정과 애드온이 동작하기를 원하는 대상 모듈을 지정할 수 있습니다.<br>
		 *                           이 정보들이 $addon_info 변수를 통해서 전달됩니다.
		 */
		public function setInfo($addon_info) { $this->addon_info = $addon_info; }

		/**
		 * 모듈 객체 생성 이전 : 사용자의 요청으로 필요한 모듈을 찾은후 모듈의 객체를 생성하기 이전을 의미합니다.
		 *
		 * @param ModuleHandler $moduleHandler
		 */
		function before_module_init(ModuleHandler $moduleHandler) { }

		/**
		 * 모듈 실행 이전 : 모듈의 객체를 실행하고 모듈의 실행을 하기 이전을 의미합니다.
		 *
		 * @param ModuleObject $moduleObject
		 */
		function before_module_proc(ModuleObject $moduleObject) {

		}

		/**
		 * 모듈의 동작 이후 : 생성된 모듈 객체를 실행하고 결과를 얻은 바로 후를 의미합니다.
		 *
		 * @param ModuleObject $moduleObject
		 */
		function after_module_proc(ModuleObject $moduleObject) {

		}

		/**
		 * 결과 출력 이전 : 모듈의 결과물과 레이아웃의 적용을 끝내고 출력하기 바로 이전을 의미합니다.
		 *
		 * @param DisplayHandler $displayHandler
		 *
		 * @param ModuleObject   $oModule
		 * @param                $handler
		 * @param                $output
		 *
		 * @return string
		 */
		function before_display_content(DisplayHandler $displayHandler, ModuleObject $oModule, $handler, $output) {
			$msg = addslashes(Context::getLang('datasaver_warning'));

            if($GLOBALS['suppress_datasaver_warning'] || Context::getRequestMethod() != 'GET')
			    return $output;

			if($this->isRequestedDatasaver() && !array_key_exists('shown_datasaver_warning', $_COOKIE)) {
				setcookie('shown_datasaver_warning', '1', time() + (60 * $this->getPeriod()));
				return $output . "<script> alert('{$msg}'); </script>";
			}

			return $output;
		}
	}
}
$addon = datasaver_warningAddon::getInstance();
$addon->setInfo($addon_info);
$addon->setPath($addon_path);

if (method_exists($addon, $called_position)) {
	if ($called_position == 'before_display_content')
		$output = $addon->before_display_content($this, $oModule, $handler, $output);
	else {
		$addon->$called_position($this);
	}
}