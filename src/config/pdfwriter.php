<?php

/**
 * Default configuration settings for the PDFWriter package
 */

return [

    /** Assumes /resources/views  */
    'pdf_blade_files' => 'pdf_templates',

    'pdf_debug' => false,

    'pdf_css' => [],

    'pdf_js' => [],

    //Need Full path to executable
    'wkhtml_path' => env('WKHTML_PATH', base_path() . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
    'wkhtml_timeout' => env('WKHTML_TIMEOUT', 60)

];