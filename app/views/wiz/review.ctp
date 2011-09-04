<h1>Review</h1>

<ol>
<?php
    
    foreach ($input as $stepName => $models) {
        echo "<li>$stepName:<ol>";
        foreach ($models as $modelName => $modelValues) {
            echo "<li>$modelName:<ol>";
            foreach ($modelValues as $fieldName => $fieldValue) {
                echo "<li><strong>$fieldName</strong>: $fieldValue</li>";
            }
            echo "</ol></li>";
        }
        echo "</ol></li>";
    }
    
?>
</ol>