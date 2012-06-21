<?php

if (empty ($λ)) $λ = 2;

if ($λ < .001)
    $colour = 'green';
elseif ($λ < .04)
    $colour = 'yellow';
elseif ($λ < 1.0)
    $colour = 'red';
else
    $colour = 'bork';


$message = sprintf ("The λ for this sample of %0.4f means ", $λ);
switch ($colour) {
    case "green":
        $message .= "depurination due to heating is unlikely to have fragmented the DNA badly. Good results may be obtainable, even using smaller sample sizes.";
        break;
    case "yellow":
        $message .= "the DNA is relatively fragmented but of sufficient quality for some types of experiment.";
        break;
    case "red":
        $message .= "the DNA is badly damaged. Destructive sampling for DNA amplification should almost certainly be avoided.";
        break;
    case "bork":
        $message .= "that unfortunately your DNA is toast - disappeared, obliterated, kaput. A λ of 1 indicates complete destruction, any higher than one is not meaningful in the same way but indicates that more time & temperature quantified depurination has taken place than is required to destroy the DNA completely.";
        break;
    default:
        $message .= "[uh oh - we're not sure - invalid data passed to template? This is an error, by the way.]";
        
}

?>
<div class="<?=@$class?>">
    <div class="clearfix" style="background-image: url('/img/<?='traffic_'.$colour?>.png'); background-repeat: no-repeat; background-position: 10px 30px; padding-left: 62px; min-width: 100px; min-height: 200px;">
        <p><?=$message?></p>
        <p>Remember, this tool cannot predict that the DNA <strong>is</strong> of good quality, only that it is unlikely to have been destroyed by temperature related depurination.</p>
    </div>
</div>