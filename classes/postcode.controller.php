<?php
/**
 * 2014 Apple Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to telco.csee@geopost.pl so we can send you a copy immediately.
 *
 * @author    JSC INVERTUS www.invertus.lt <help@invertus.lt>
 * @copyright 2014 DPD Polska sp. z o.o.
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of DPD Polska sp. z o.o.
 */

if (!defined('_PS_VERSION_'))
    exit;


class DpdGeopostPostcodeController extends DpdGeopostController
{
    const DEFAULT_ORDER_BY = 'id_shipment';
    const DEFAULT_ORDER_WAY = 'desc';
    const FILENAME = 'shipmentsList.controller';

    public function __construct()
    {
        parent::__construct();

    }


    public function getPostcodeUpdateForm()
    {
        $configuration = new DpdGeopostConfiguration();

        $this->context->smarty->assign(array(
            'saveAction'          => $this->module_instance->module_url,
            'available_countries' => $configuration->countries,
            'settings'            => $configuration,
            'uploadedFiles'       => $this->getUploadedImportFiles()
        ));


        return $this->context->smarty->fetch(_DPDGEOPOST_TPL_DIR_ . 'admin/postcode_update_form.tpl');
    }


    public function uploadAndImport()
    {
        set_time_limit(0);

        $module_instance = Module::getInstanceByName('dpdgeopost');

        $name = 'csv';

        $status = array();


        if (isset($_FILES[$name]['name']) && !empty($_FILES[$name]['name'])) {
            $filename = basename(html_entity_decode($_FILES[$name]['name'], ENT_QUOTES, 'UTF-8'));

            if ((strlen($filename) < 3) || (strlen($filename) > 128)) {
                $status['error'] = $this->l('The filename is probably wrong. ');
            }

            // Allowed file extension types
            $allowed = array('csv');
            $ext     = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                $status['error'] = $this->l('File extension is wrong, please provide a csv file.');
            }

            // Allowed file mime types
            $allowed = array('csv', 'application/vnd.ms-excel');

            if (!in_array($_FILES[$name]['type'], $allowed)) {
                //skip mime type validation
                //$json['error'] = $this->l('error_filetype');
            }

            if (!empty($_FILES[$name]['tmp_name'])) {
                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($_FILES[$name]['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $status['error'] = $this->l('File content is wrong. Please check your CSV content.');
                }
            }

            if ($_FILES[$name]['error'] != UPLOAD_ERR_OK) {
                $status['error'] = $this->l($this->getUploadCodeMessage($_FILES[$name]['error']));
            }
        } else {
            $status['error'] = $this->l('File was not uploaded. Please check the upload_max_filesize setting of your server. upload_max_filesize: ' . ini_get('upload_max_filesize'));
        }

        $postcodeSearch = new DpdGeopostPostcodeSearch();
        if (!isset($status['error'])) {
            if (is_uploaded_file($_FILES[$name]['tmp_name']) && file_exists($_FILES[$name]['tmp_name'])) {
                $status['filename'] = $filename;
                $status['mask']     = $filename;

                $path = $postcodeSearch->getPathToDatabaseUpgradeFiles();
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                move_uploaded_file($_FILES[$name]['tmp_name'], $path . $filename);
            }

            $status['success'] = $this->l('Postcode database was successfully updated.');
        }


        if (!isset($status['error'])) {
            try {
                $postcodeSearch->updateDatabase($path . $filename);
            } catch (Exception $e) {
                $module_instance->addFlashError($module_instance->l($e->getMessage()));
                Tools::redirectAdmin($this->module_instance->module_url . '&menu=postcodeUpdate');

                return;
            }
        }


        if (isset($status['success'])) {
            $module_instance->addFlashMessage($module_instance->l($status['success']));
        } else {
            $module_instance->addFlashError($module_instance->l($status['error']));
        }

        Tools::redirectAdmin($this->module_instance->module_url . '&menu=postcodeUpdate');


    }

    /**
     * used to import a csv file by name
     * file should exist in upload/dpd/postcode_update folder
     */
    public function import()
    {
        set_time_limit(0);

        try {
            $module_instance = Module::getInstanceByName('dpdgeopost');
            $postcodeSearch = new DpdGeopostPostcodeSearch();

            $file = Tools::getValue('file_path');
            if (empty($file)) {
                throw new Exception($this->language->get('Provide a file name from the list below to be used as a DPD postcode database.'));
            }

            $file = basename($file);
            $path = $postcodeSearch->getPathToDatabaseUpgradeFiles();
            if (!is_file($path . $file)) {
                throw new Exception($this->language->get('The file does not exit in the specified path: download/dpd/postcode_updates'));
            }

            // Allowed file extension types
            $allowed = array('csv');
            $ext     = pathinfo($file, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                throw new Exception($this->language->get('Wrong file extension, please provide a csv file.'));
            }

            $postcodeSearch->updateDatabase($path . $file);

        } catch (Exception $e) {
            $module_instance->addFlashError($module_instance->l($e->getMessage()));
            Tools::redirectAdmin($this->module_instance->module_url . '&menu=postcodeUpdate');

            return;
        }

        $module_instance->addFlashMessage($module_instance->l('Postcode database was successfully updated.'));
        Tools::redirectAdmin($this->module_instance->module_url . '&menu=postcodeUpdate');
    }


    public function getUploadedImportFiles()
    {
        $postcodeSearch = new DpdGeopostPostcodeSearch();
        $path           = $postcodeSearch->getPathToDatabaseUpgradeFiles();

        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $ext = pathinfo($entry, PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'csv') {
                    continue;
                }
                $files[$entry] = filemtime($path . $entry);
            }
            closedir($handle);
        }
        asort($files);

        return $files;
    }


    private function getBackUrl()
    {
        return $this->module_instance->module_url . '&menu=postcodeUpdate';
    }

    /**
     *
     * @param $code
     *
     * @return string
     */
    private function getUploadCodeMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }

        return $message;
    }


}