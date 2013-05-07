<?php
//print_r ($this->getVar ('report'));

$job = $this->getVar ('job');
if (is_array ($job)) {
    
}
$report = $this->getVar ('report');
if (is_array ($report)) {
    $drawGraphs = array ();
    //$rgbPath = $this->getVar ('rgbPath');
    $rgbPath = '';
    if (!empty ($report['graphs'])) {
        foreach ($report['graphs'] as $gt => $gu) {
            $gu = basename ($gu);
            $drawGraphs[$gt] = array (
                'name' => $gt,
                'svg' => $rgbPath . $gu,
                'pdf' => $rgbPath . preg_replace ('/\.svg$/i', '.pdf', $gu),
                'png' => $rgbPath . preg_replace ('/\.svg$/i', '.png', $gu)
            );
        }
    }
    $graphs = array (
        'lambda' => array (
            'title' => 'Distribution of DNA fragment lengths',
            'width_cm' => '12'
        ),
        'burial' => array (
            'title' => 'Temperature and rate of reaction over time',
            'width_cm' => '14'
        ),
        'histogram' => array (
            'title' => 'Temperature of days modelled',
            'width_cm' => '14'
        ),
    );
}

if (!function_exists('opgraph')) {
    function opgraph ($name, $graphs, $drawGraphs) {
        //if (!isset ($drawGraphs[$name])) return false;
        $graph = $drawGraphs[$name];
        ?>
        \begin{figure}
         \centering
         \includegraphics[width=<?=$graphs[$name]['width_cm']?>cm]{<?=$graph['pdf']?>}
         \caption{<?=$graphs[$name]['title']?>}
         (Attached: \attachfile[appearance=false]{<?=$graph['svg']?>}{SVG}, \
         \attachfile[appearance=false]{<?=$graph['png']?>}{PNG}, \
         \attachfile[appearance=false]{<?=$graph['pdf']?>}{PDF})
         \label{fig:graph_<?=$name;?>}
        \end{figure}
        <?php
    }

}

?>
\documentclass[11pt,a4paper]{report}

\usepackage[latin1]{inputenc}
\usepackage{amsmath}
\usepackage{amsfonts}
\usepackage{amssymb}
\usepackage{cite}
\usepackage{longtable}
%\usepackage{harvard}
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
%\maketitle
%\pagenumbering{roman}
%\clearpage
%\listoftables
%\listoffigures
%\clearpage



\maketitle
\begin{abstract}
    \begin{center}
        \begin{tabular}{ | r | l |}
        \hline
        \textbf{Job ID} & <?=$job['Job']['id']?> \\ \hline
        <?php if (!!$job['Job']['published']) {
            $jurls = "/pub/" . $job['Job']['pub_ref'];
            $jurl = $this->Html->url ($jurls, true);
            ?>
        Public & Yes \\
        Embargo Until & <?=date('l, jS M Y', strtotime($job['Job']['published_date']))?> \\
        Reference & <?=$job['Job']['pub_ref']?> \\
        Stable URL & \href{<?=$jurl?>}{<?=$jurl?>} \\
        <?php } ?>
        \hline
        Job Created & <?=date('l, jS M Y', strtotime($job['Job']['created']))?> \\
        <?php if ($job['Job']['created'] != $job['Job']['updated']) { ?>
        Last Modified & <?=date('l, jS M Y', strtotime($job['Job']['updated']))?> \\ 
        <?php } ?>
        \hline
        \end{tabular}
    \end{center}
    
    \vspace{2em}
    
    This report contains information submitted to one of the thermal history tools on the 
    \href{http://thermal-age.eu/}{thermal-age.eu} website and the results of calculations based on these
    data which aim to estimate the gross amount of chemical reactions taking place over time in 
    archaeological specimens.

    The thermal-age.eu model and web tool is an output of JRA1
    \footnote{See \href{http://www.synthesys.info/II_JRA_1.htm}{http://www.synthesys.info/II\_JRA\_1.htm}.}
    of the EU SYNTHESYS\footnote{See \href{http://www.synthesys.info/}{http://www.synthesys.info/}.} project.
    
    The predictive model and web application software was developed by David Harker\footnote{Technical contact \href{mailto:david@wtds.co.uk}{david@wtds.co.uk}. Developed the algorithms and modelling software and created the web application. See \href{http://wtds.co.uk/?src=taeupdf}{http://wtds.co.uk/}.}
    and Matthew Collins\footnote{Academic contact \href{mailto:matthew.collins@york.ac.uk}{matthew.collins@york.ac.uk}. Initial concept\cite{oldcold} \& design of model, development guidance and data synthesis/analysis. See \href{https://www.york.ac.uk/archaeology/staff/academic-staff/matthew-collins/}{https://www.york.ac.uk/archaeology/staff/academic-staff/matthew-collins/}.}
    at BioArCh\footnote{BioArCh provides archaeologists with analytical facilities. BioArCh is a joint venture between the Departments of Biology, Archaeology, and Chemistry at the University of York. See \href{http://www.york.ac.uk/palaeo/facilities/bioarch/}{http://www.york.ac.uk/palaeo/facilities/bioarch/}.}, 
    University of York\footnote{See \href{http://www.york.ac.uk/}{http://www.york.ac.uk/}}.
    
    \vspace{1em}
    
    \textbf{Important: Any use you make of these figures in your decision making (e.g. allocating
    funds) is entirely at your own risk.} The thermal-age.eu tools are based on estimates of past temperatures
    interpolated from publicly available data sources. All figures produced by the software are
    estimates only. Many factors besides temperature affect the rate of chemical reactions and the
    model does not attempt to take these into account. At best it can tell you which specimens are 
    more likely to fail.
    
    
    
\end{abstract}

\tableofcontents
<?php
$s =& $job['Job']['data']['specimen'];
$data =& $job['Job']['data'];
?>

\chapter{Context of Specimen}

\section{General}
    <?php if (!empty ($s['Specimen']['description'])) {
        echo "Description:";
        echo "\begin{quote}\n";
        echo $s['Specimen']['description'];
        echo "\end{quote}\n";
    }
    ?>
    \begin{center}
        \begin{tabular}{ | r | p{6cm} |}
        \hline
        \textbf{Job ID} & <?=$job['Job']['id']?> \\ \hline
        \textbf{Specimen Name} & <?=$s['Specimen']['name']?> \\ 
        <?=(!empty ($s['Specimen']['code'])) ? '\textbf{Lab Ref} & '.$s['Specimen']['code'].' \\\\' : '' ?>
        \hline
        \textbf{Age Today, <?=date('Y')?>} & <?=$s['Temporothermal']['stopdate_ybp']+(date('Y')-1950)?> years \\ \hline
        \textbf{Reaction} & <?=$data['reaction']['Reaction']['showname']?> \\ \hline
        \end{tabular}
    \end{center}
    
    

\section{Site}
    
    <?php if (!empty ($data['site']['Site']['description'])) {
        echo "Description:";
        echo "\begin{quote}\n";
        echo $data['site']['Site']['description'];
        echo "\end{quote}\n";
    }
    ?>
    \begin{center}
        \begin{tabular}{ | r | p{6cm} |}
        \hline
        \textbf{Site Name} & <?=$data['site']['Site']['name']?> \\ \hline
        \textbf{Latitude} & <?=sprintf('%02.6f',$data['site']['Site']['lat_dec'])?> $^\circ$N \\ 
        \textbf{Longitude} & <?=sprintf('%03.6f',$data['site']['Site']['lon_dec'])?> $^\circ$E \\ 
        \hline
        <?php if (!empty ($data['site']['Site']['elevation'])) { ?>
        \textbf{Elevation AMSL WGS84} & <?=sprintf('%01.2f',$data['site']['Site']['elevation'])?> m\\ 
        \textbf{Source} & <?=$data['site']['Site']['elevation_source']?>\\ 
        \hline
        <?php } ?>
        
        \end{tabular}
    \end{center}
    
\subsection{Burial Layers}

Burial protects a specimen from the extremes of hot and cold at the surface. The specimen will have the
same mean temperature as the surface, but it won't get as hot or as cold.

<?php

$b = $data['burial'];

$nb = count ($b['SoilTemporothermal']);

if ($nb == 0 || $nb == false) {
    echo "This specimen was not recorded as being buried under any layers of sediment.";
}
else {
    $nbp = ($nb == 1) ? '' : 's';
    echo "This specimen was recorded as being buried under $nb layer{$nbp} of sediment.\n\n";
    ?>
\begin{center}
    \begin{tabular}{ | l | c | c | c |}
    \hline
    \textbf{Soil Type} & \textbf{Thickness} & \textbf{Mass $H_2O$} & \textbf{Thermal Diffusivity} \\ 
                       & $m$                & \%                   & $m^2 \text{day}^{-1}$ \\ \hline\hline
    <?php
    foreach ($b['SoilTemporothermal'] as $li => $stl) {
        $sl = $b['Soil'][$li];  
        printf ("%s & %01.2f & %01.1f & %01.5f \\\\ \n", $sl['name'], $stl['thickness_m'], $sl['water_content'], $sl['thermal_diffusivity_m2_day']);
    }
    ?>
                       
    \hline
    
    \end{tabular}
\end{center}
<?php
}

?>

<?php
/*
 * 
 * job = Array
(
    [Job] => Array
        (
            [id] => 698
            [user_id] => 1
            [title] => STD-0; 4000yrs bp, S-Block, University of Yo...
            [data] => Array
                (
                    [specimen] => Array
                        (
                            [Specimen] => Array
                                (
                                    [name] => "Standard" Run
                                    [code] => STD-0
                                    [description] => This run contains standard values for use as "sane defaults" when comparing specimens where some variables may be unknown.
                                )

                            [Temporothermal] => Array
                                (
                                    [stopdate_ybp] => 4000
                                )

                        )

                    [reaction] => Array
                        (
                            [Reaction] => Array
                                (
                                    [showname] => DNA Depurination (Bone)
                                    [reaction_id] => 1
                                    [molecule_name] => 
                                    [reaction_name] => 
                                    [substrate_name] => 
                                    [name] => 
                                    [ea_kj_per_mol] => 
                                    [f_sec] => 
                                    [citation_id] => 3
                                )

                        )

                    [site] => Array
                        (
                            [Site] => Array
                                (
                                    [name] => S-Block, University of York
                                    [lat_dec] => 53.94679973582498
                                    [lon_dec] => -1.0580287460327327
                                    [elevation] => 14
                                    [elevation_source] => Wikipedia
                                    [lapse_correct] => 1
                                    [description] => The University of York (informally York University, or simply York, abbreviated as Ebor. for post-nominals), is an academic institution located in the city of York, England. Established in 1963, the campus university has expanded to more than thirty departments and centres, covering a wide range of (...)
(elevation: 14 m)
                                )

                        )

                    [burial] => Array
                        (
                            [Temporothermal] => Array
                                (
                                    [startdate_yad] => 2012
                                    [startdate_ybp] => -62
                                    [temp_mean_c] => 
                                    [temp_pp_amp_c] => 
                                    [description] => 
                                )

                            [Burial] => Array
                                (
                                    [numLayers] => 1
                                )

                            [SoilTemporothermal] => Array
                                (
                                    [0] => Array
                                        (
                                            [thickness_m] => 2
                                            [soil_id] => 19
                                            [custom] => 0
                                            [order] => 0
                                        )

                                )

                            [Soil] => Array
                                (
                                    [0] => Array
                                        (
                                            [name] => Silt Loam
                                            [percent_saturated_h2o] => 0.15
                                            [water_content] => 15
                                            [thermal_diffusivity_m2_day] => 0.044928
                                        )

                                )

                        )

                    [storage] => Array
                        (
                            [Temporothermal] => Array
                                (
                                    [startdate_yad] => 2013
                                    [startdate_ybp] => -63
                                    [stopdate_ybp] => -55
                                    [temp_mean_c] => 10
                                    [temp_pp_amp_c] => 10
                                    [description] => 
                                )

                        )

                    [review] => Array
                        (
                            [Job] => Array
                                (
                                    [processor_name] => thermal_age
                                    [parser_name] => dna_screener
                                    [reporter_name] => dna_screener
                                )

                        )

                )

            [processor_name] => thermal_age
            [parser_name] => dna_screener
            [reporter_name] => dna_screener
            [created] => 2013-05-04 06:47:27
            [updated] => 2013-05-05 06:11:22
            [pub_ref] => TAEU-S698
            [status] => 2
            [priority] => 1
            [published] => 1
            [published_date] => 2013-05-06 00:00:00
            [wizard_name] => dna_survival_screening_tool
        )

    [User] => Array
        (
            [id] => 1
            [name] => David Harker
            [username] => david
            [password] => a7809e0744845aefb354a02b14c6e0f17601409e
            [alias] => David
            [email_priv] => thermal-age@dhharker.net
            [url] => http://dhharker.co.uk/
            [institution] => University of York
            [bio] => I'm the author, hello.
            [created] => 2011-07-17 17:34:59
            [modified] => 2011-07-17 17:34:59
            [group_id] => 0
            [photo] => /uploads/view/151
            [oauth_linked] => 
        )

    [LabResult] => Array
        (
        )

)
 * 
Array
(
    [summary] => Array
        (
            [λ] => 0.0064553336912149
            [(1/λ)+1] => 155.91065959315
            [k (yr)] => 1.5860770740086E-6
            [k (sec)] => 5.029417408703E-14
            [Teff] => 8.4301917528571
            [Thermal age] => 3013.315818889
        )

    [graphs] => Array
        (
            [lambda] => reports/698_lambdas_fragment_lengths.svg
            [burial] => reports/698_temporothermal_1_graph.svg
        )


)
 */
?>

<?php
$r = $report['summary'];
?>

\chapter{Results}

\section{Summary}

The following table summarises the output of the model for this specific sample. Please see the important disclaimer at the beginning of this document.
\begin{center}
    \begin{tabular}{ | r | c | c | l |}
    \hline
    \textbf{Variable}       & \textbf{Symbol}    & \textbf{Value}                           & \textbf{Units}                            \\ \hline
    Lambda                  & $\lambda$         & $<?=sprintf('%0.5f', $r['λ'])?>$          & /                                         \\ \hline
    Effective Rate          & $k$               & $<?=sprintf('%0.2E', $r['k (sec)'])?>$    & $\text{mol}\,\cdot\,\text{sec}^{-1}$      \\ 
                            &                   & $<?=sprintf('%0.2E', $r['k (yr)'])?>$     & $\text{mol}\,\cdot\,\text{year}^{-1}$     \\ \hline
    Effective Temperature   & $T_{\text{eff}}$  & $<?=sprintf('%0.1f', $r['Teff'])?>$       & $^\circ{}C$                               \\ \hline
    Thermal Age             & $T_{\text{eff}}$  & $<?=sprintf('%d',$r['Thermal age'])?>$    & $10^\circ$C Thermal Years                 \\ \hline
    
    \end{tabular}
\end{center}
The next sections explain the meaning of these values and what you can do with them.


\subsection{Lambda}

The lambda ($\lambda$) value for the specimen is \textbf{<?=sprintf('%0.5f', $r['λ'])?>}.
Lambda expresses the product of $k$ (effective rate of reaction in $\text{mol}\,\cdot\,\text{sec}^{-1}$)
and $t$ ($\text{sec}$) i.e. the effect of temperature integrated over time.

The value of $k$ for this specimen is $<?=sprintf('%0.2E', $r['k (sec)'])?>\,\text{mol}\,\cdot\,\text{sec}^{-1}$
(or $<?=sprintf('%0.2E', $r['k (yr)'])?>\,\text{mol}\,\cdot\,\text{year}^{-1}$). This equates to an
\textit{Effective Temperature} ($T_{\text{eff}}$) of $<?=sprintf('%0.1f', $r['Teff'])?>^\circ$C

The effective temperature is the temperature at which the sample could have been kept for its entire
post-depositional lifespan in order to incur the same $\lambda$ (i.e. level of DNA damage due to
temperature-limited spontaneous depurination) value as it did in its actual environment.


\subsection{Thermal Age}

\textit{Thermal Age} takes this one step further. It is calculated by normalising time to the rate of
reaction at a given reference temperature (a constant $10^\circ$C). The units are ``$10^\circ$C
Thermal Years'' and the quantity expresses the number of years stored at a constant $10^\circ$C which
would incur the same $\lambda$ value as the specimen has done.

\subsection{DNA Fragmentation}

As well as being $k\times{}t$, $\lambda$ is also the probability that any single bond between the
base pairs of a DNA molecule will have broken. It follows that the probability of the bond \textit{not}
having broken is $1-\lambda$ and the probability of a fragment of DNA of length $l$ having survived
is then:
$$
P(l)\,=\,(1-\lambda)^{l-1}
$$
Assuming a random scission model of DNA fragmentation, the \textit{mean fragment length} is given as:
$$
\overline{l}\,=\,\frac{1}{\lambda}\,+1
$$
In the case of the specimen described in this report, the mean fragment length is
\textbf{<?=sprintf('%1.1f', $r['(1/λ)+1'])?>} base pairs. Figure \ref{fig:graph_lambda} shows the relative probability of
recovering a fragment of DNA of a given length from this sample, the cutoff below which DNA is
completely destroyed and possibly one or more examples to provide an intuitive comparison.

<?php
opgraph('lambda', $graphs, $drawGraphs);
?>


\section{Temperature}

The thermal-age.eu model works by correcting the global average temperature and temperature amplitude 
from \cite{bintanja} against geolocal estimates of ``modern'' (pre-industrial), mid-holocene and last
glacial maximum temperatures from PMIP2 \cite{pmipii}.

<?php
opgraph('burial', $graphs, $drawGraphs);
?>

Figure \ref{fig:graph_burial} shows the average temperature, effective temperature and temperature
variation both at the ground surface and below ground, if burial has been configured.

The thermal age model samples up to 500 evenly spaced years from the interpolated temperature history
of the specimen (this allows for a good compromise between accuracy and computational intensity).
The temperature on each day of each of the sampled years is then binned and, once all days have been
processed, the rate of reaction for each of the bin temperatures is calculated and this is used to
arrive at the effective rate of reaction ($k$). Figure \ref{fig:graph_histogram} shows the number of
days at each bin temperature used for the calculations in this report. The temperatures in this
figure take into account burial where specified.

<?php
opgraph('histogram', $graphs, $drawGraphs);
?>



\appendix

\chapter{About This Report}

This report has been generated automatically by the 
\href{http://thermal-age.eu}{http://thermal-age.eu} web application. The calculations were performed
by TTKPL (Temporo-Thermal Kinetic Prediction Library). Both the web application and library are
Open Source Software under the EU Public License and you can access the source code at
\url{https://github.com/dhharker/thermal-age.eu}
and
\url{https://github.com/dhharker/ttkpl}
respectively. If you have noticed an error or would like to contribute an improvement then please
create an issue or submit a pull request in the relevant github repository (above).

\section{Third Party Datasets Used}

The thermal age model makes use of several third party datasets and web based data sources; 



\section{Terms \& Conditions of Use}



\bibliographystyle{plain}
\bibliography{rptbib}

\end{document}