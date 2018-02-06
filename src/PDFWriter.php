<?php
namespace Greenleaf\PDFWriter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Knp\Snappy\Pdf;

class PDFWriter
{
    /**
     * @var Pdf Snappy PDF Library instance
     */
    protected $snappy;

    /**
     * @var string The Blade PDF template to use
     */
    protected $template;

    /**
     * @var mixed Data to be passed to
     */
    protected $data;

    /**
     * @var string $filename
     */
    protected $filename;

    /**
     * @var string $headerLogo
     */
    protected $headerLogo;

    /**
     * @var string $customHeader Custom header variable
     */
    protected $customHeader;
    protected $customFooter;

    /**
     * @var bool $showPageNumbers Whether or not to show page numbers in the footer area
     */
    protected $showPageNumbers = false;

    /**
     * @var bool $showDate Whether or not to show dates in the footer area
     */
    protected $showDate = false;

    /**
     * PDFWriter constructor.
     * @param string $template blade template file
     * @param Model $data Some Eloquent model record that we're passing into the view for creating the PDF
     * @param string $filename The file name you'd like the pdf to be saved as
     */
    public function __construct($template, $data, $filename = 'file.pdf', $withHeaaderLogo = false, $wkhtmltopdfPath = null, $timeout = null)
    {
        $this->template = $template;
        $this->data = $data;
        $this->filename = $filename;
        $this->headerLogo = $withHeaaderLogo;

        if($wkhtmltopdfPath !== null) {
            $this->snappy = new Pdf($wkhtmltopdfPath);
        } else {
            $this->snappy = new Pdf(\Config::get('pdfwriter.wkhtml_path'));
        }

        if($timeout !== null) {
            $this->snappy->setTimeout($timeout);
        }else {
            $this->snappy->setTimeout(\Config::get('pdfwriter.wkhtml_timeout'));
        }
    }


    /**
     * Add custom header HTML to the PDF Writer
     *
     * @param string $html
     * @return $this
     */
    public function customHeaderHTML($html = '') {
        $this->customHeader = $html;

        return $this;
    }

    /**
     * Add custom Footer HTML to the PDF Writer
     *
     * @param string $html
     * @return $this
     */
    public function customFooterHTML($html = '') {
        $this->customFooter = $html;

        return $this;
    }

    /**
     * Show page numbers at the bottom of PDFs
     *
     * @return $this
     */
    public function showPages() {
        $this->showPageNumbers = true;

        return $this;
    }

    /**
     * Show the current date in the footer of the PDF
     *
     * @return $this
     */
    public function showDates() {
        $this->showDate = true;

        return $this;
    }

    /**
     * Set the orientation of the PDF
     *
     * @param string $orientation
     * @return $this
     */
    public function setOrientation($orientation="Portrait")
    {
        $this->snappy->setOption('orientation',$orientation);

        return $this;
    }

    /**
     * Returns the raw
     *
     * @return mixed
     * @throws \Exception
     */
    public function getPDFOutput()
    {
        $this->header();
        $this->footer();

        return $this->snappy->getOutputFromHtml($this->populateTemplate());
    }

    /**
     * Prints the PDF inline
     *
     * @throws \Exception
     */
    public function printInline()
    {

        $this->header();
        $this->footer();

        if (\Config::get('pdfwriter')['pdf_debug']) {
            $options = $this->snappy->getOptions();
            $html = $options['header-html'];
            $html .= $this->populateTemplate();
            $html .= $options['footer-html'];
            echo $html;
            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $this->filename . '"');

        echo $this->snappy->getOutputFromHtml($this->populateTemplate());
    }

    /**
     * Saves the PDF to the file path you specify
     *
     * @param $filepath
     * @throws \Exception
     */
    public function save_to_file($filepath) {

        $this->header();
        $this->footer();

        $this->snappy->generateFromHtml($this->populateTemplate(), $filepath . '/' . $this->filename);
    }

    /**
     * Header function to build the PDF Header
     */
    public function header()
    {

        if($this->headerLogo && empty($this->customHeader)) {
            $this->snappy->setOption('header-html', '<!DOCTYPE html><body><img src="' . $this->headerLogo . '" /></body></html>');
            $this->snappy->setOption('header-spacing', 5);
        }else {
            $this->snappy->setOption('header-html', $this->customHeader);
            $this->snappy->setOption('header-spacing', 5);
        }
    }

    /**
     * Footer function to build the PDF footer
     */
    public function footer()
    {

        if($this->customFooter) {
            $this->snappy->setOption('footer-html', $this->customFooter);
        }else{

            if($this->showDate) {
                $this->snappy->setOption('footer-left', \Carbon::now()->format('m/d/Y'));
            }

            if($this->showPageNumbers) {
                $this->snappy->setOption('footer-right', 'Page [page] of [topage]');
            }

        }

    }


    /**
     * This function uses the blade template engine for populating the PDF.
     *s
     * @return string
     * @throws \Exception
     */
    protected function populateTemplate()
    {
        $content = view(\Config::get('pdfwriter')['pdf_blade_files'] . '.' . $this->template, ['record'=> $this->data])->render();

        return $content;
    }
}
