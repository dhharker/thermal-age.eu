<?php
//print_r ($this->getVar ('report'));

$job = $this->getVar ('job');
if (is_array ($job)) {
    
}
$report = $this->getVar ('report');
if (is_array ($report)) {
    $drawGraphs = array ();
    $rgbPath = $this->getVar ('rgbPath');
    if (!empty ($report['graphs'])) {
        foreach ($report['graphs'] as $gt => $gu) {
            $drawGraphs[] = array (
                'name' => $gt,
                'svg' => $rgbPath . $gu,
                'pdf' => $rgbPath . preg_replace ('/\.svg$/i', '.pdf', $gu)
            );
        }
    }
    $graphs = array (
        'lambda' => array (
            'title' => 'Distribution of DNA Fragment Lengths',
            'width_cm' => '12'
        ),
        'burial' => array (
            'title' => 'Temperature Over Time',
            'width_cm' => '14'
        ),
    );
}

?>
\documentclass[11pt,a4paper]{report}

\usepackage[latin1]{inputenc}
\usepackage{amsmath}
\usepackage{amsfonts}
\usepackage{amssymb}
\usepackage{cite}
\usepackage{longtable}
\usepackage{graphicx}
\usepackage{setspace}
%uncomment for linked PDFs:
\usepackage{url}
\usepackage{hyperref}
\usepackage{rotating}

\usepackage{attachfile}


% uncomment for hyperref settings
\hypersetup{%
pdftitle={<?=$this->getVar('title')?>},%
pdfauthor={<?=$this->getVar('author')?>},%
pdfkeywords={<?=$this->getVar('keywords')?>},%
bookmarksnumbered,%
pdfstartview={FitH},%
colorlinks=true,%
linkcolor=blue,%
%sitecolor=black,%
filecolor=green,%
urlcolor=blue,%
}%
\title{<?=$this->getVar('title')?>}
\author{<?=$this->getVar('author')?>}

\begin{document}
\pagenumbering{arabic}
\maketitle
%\pagenumbering{roman}
%\clearpage
%\tableofcontents
%\listoftables
%\listoffigures
%\clearpage

%\begin{abstract}
%Buried under X, X years old, DNA is X\% fucked etc.
%\end{abstract}


\chapter{Summary}

ASDF!

\section{Introduction}

Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.



Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.

Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.

Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.
<?php
foreach ($drawGraphs as $gi => $graph) {
?>
\begin{figure}
 \centering
 \includegraphics[width=<?=$graphs[$graph['name']]['width_cm']?>cm]{<?=$graph['pdf']?>}
 \caption{<?=$graphs[$graph['name']]['title']?>}
 (Graph SVG \attachfile[appearance=false,icon=Paperclip]{<?=$graph['svg']?>})
 \label{fig:graph_<?=$graph['name'].'_'.$gi;?>}
\end{figure}
<?php
}
?>

Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.

Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes. Your sample is very fresh. Season with olive oil, sage and parsely and roast at 210 for 70 minutes.



\chapter{Breakdown}
Rr-r-r-rrrewind!

\appendix

\chapter{About This Report}
This report has been generated automatically using tools at \href{http://thermal-age.eu}{thermal-age.eu} including the \href{https://github.com/dhharker/ttkpl}{ttkpl} palaeotemperature estimation and kinetic calculation library.



\end{document}