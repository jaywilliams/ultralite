<?php
 

    /**
     * Class: Pixelpost_Config
     * Holds all of the configuration variables for the entire site, as well as Module settings.
     */
    class Pixelpost_Config {
        # Variable: $yaml
        # Holds all of the YAML settings as a $key => $val array.
        private $yaml = array();

        /**
         * Function: __construct
         * Loads the configuration YAML file.
         */
        private function __construct() {
            if (!file_exists(APPLICATION_PATH."/configs/pixelpost.yaml.php"))
                return false;

            $contents = str_replace("<?php header(\"Status: 403\"); exit(\"Access denied.\"); ?>\n",
                                    "",
                                    file_get_contents(APPLICATION_PATH."/configs/pixelpost.yaml.php"));

            $this->yaml = Horde_Yaml::load($contents);

            $arrays = array("enabled_modules", "enabled_feathers", "routes");
            foreach ($this->yaml as $setting => $value)
                if (in_array($setting, $arrays) and empty($value))
                    $this->$setting = array();
                elseif (!is_int($setting))
                    $this->$setting = (is_string($value)) ? stripslashes($value) : $value ;

            // fallback($this->url, $this->chyrp_url);
        }

        /**
         * Function: set
         * Adds or replaces a configuration setting with the given value.
         *
         * Parameters:
         *     $setting - The setting name.
         *     $value - The value.
         *     $overwrite - If the setting exists and is the same value, should it be overwritten?
         */
        public function set($setting, $value, $overwrite = true) {
            if (isset($this->$setting) and $this->$setting == $value and !$overwrite)
                return false;

            if (isset($this->file) and file_exists($this->file)) {
                $contents = str_replace("<?php header(\"Status: 403\"); exit(\"Access denied.\"); ?>\n",
                                        "",
                                        file_get_contents($this->file));

                $this->yaml = Horde_Yaml::load($contents);
            }

            # Add the setting
            $this->yaml[$setting] = $this->$setting = $value;

            if (class_exists("Trigger"))
                Trigger::current()->call("change_setting", $setting, $value, $overwrite);

            # Add the PHP protection!
            $contents = "<?php header(\"Status: 403\"); exit(\"Access denied.\"); ?>\n";

            # Generate the new YAML settings
            $contents.= Horde_Yaml::dump($this->yaml);

            if (!@file_put_contents(APPLICATION_PATH."/configs/pixelpost.yaml.php", $contents)) {
                Flash::warning(_f("Could not set \"<code>%s</code>\" configuration setting because <code>%s</code> is not writable.",
                                  array($setting, "/app/configs/pixelpost.yaml.php")));
                return false;
            } else
                return true;
        }

        /**
         * Function: remove
         * Removes a configuration setting.
         *
         * Parameters:
         *     $setting - The name of the setting to remove.
         */
        public function remove($setting) {
            if (isset($this->file) and file_exists($this->file)) {
                $contents = str_replace("<?php header(\"Status: 403\"); exit(\"Access denied.\"); ?>\n",
                                        "",
                                        file_get_contents($this->file));

                $this->yaml = Horde_Yaml::load($contents);
            }

            # Add the setting
            unset($this->yaml[$setting]);

            # Add the PHP protection!
            $contents = "<?php header(\"Status: 403\"); exit(\"Access denied.\"); ?>\n";

            # Generate the new YAML settings
            $contents.= Horde_Yaml::dump($this->yaml);

            file_put_contents(APPLICATION_PATH."/configs/pixelpost.yaml.php", $contents);
        }

        /**
         * Function: current
         * Returns a singleton reference to the current configuration.
         */
        public static function & current() {
            static $instance = null;
            return $instance = (empty($instance)) ? new self() : $instance ;
        }
    }
