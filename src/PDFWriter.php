<?php
namespace Greenleaf\PDFWriter;

use Knp\Snappy\Pdf;

class PDFWriter
{
    protected $template;

    protected $data;

    public function __construct($template, $data)
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function printInline()
    {
        $snappy = new Pdf(base_path() . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');

        $snappy->setOption('header-html', '<!DOCTYPE html><body><img src="' .
            base_path() . '/public/images/header_logo.png" /></body></html>');
        $snappy->setOption('header-spacing', 5);

        $this->populateTemplate();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="file.pdf"');

        echo $snappy->getOutputFromHtml($this->populateTemplate());
    }

    /**
     * This function uses the blade template engine for populating the PDF.
     * http://stackoverflow.com/questions/16891398/is-there-any-way-to-compile-a-blade-template-from-a-string
     *
     * @return string
     * @throws \Exception
     */
    protected function populateTemplate()
    {
        $generated = \Blade::compileString(
            file_get_contents(base_path() . '/resources/views/pdf_templates/' . $this->template)
        );

        //Set environment for view
        $__env = view();

        ob_start() and extract($this->data->toArray(), EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try {
            eval('?>'.$generated);
        }

        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (\Exception $e) {
            ob_get_clean();
            throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }
}
