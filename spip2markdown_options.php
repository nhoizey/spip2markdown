<?php
function spip2markdown($text) {
  // Intertitres
  // SPIP     : {{{…}}}
  // Kramdown : ## …
  $text = preg_replace("/(^|[^{]){{{([^}]+)}}}([^}]|$)/u", "$1\n## $2\n$3", $text);

  // Gras
  // SPIP     : {{…}}
  // Kramdown : **…**
  $text = preg_replace("/(^|[^{]){{([^}]+)}}([^}]|$)/u", "$1**$2**$3", $text);

  // Italique
  // SPIP     : {…}
  // Kramdown : *…*
  $text = preg_replace("/(^|[^{]){([^}]+)}([^}]|$)/u", "$1*$2*$3", $text);

  // Liens
  // SPIP     : [intitulé->lien]
  // Kramdown : [intitulé](lien)
  $text = preg_replace("/(^|[^\[])\[([^\]\[]+)->([^\]\[]+)\]([^\]]|$)/u", "$1[$2]($3)$4", $text);

  // Notes de bas de page
  // SPIP     : [[…]]
  // Kramdown : [^n] dans le texte et [^n]: … en fin de contenu
  $text = preg_replace("/(^|[^\[])\[\[([^\[]|$)/u", "$1☞$2", $text);
  $text = preg_replace("/(^|[^\]])\]\]([^\]]|$)/u", "$1☜$2", $text);
  $count = 0;
  $notes = "\n";
  $text = preg_replace_callback(
    "/☞([^☜]+)☜/u",
    function($match) use (&$count, &$notes) {
      $count++;
      $notes .= "\n\n[^" . $count . "]: " . $match[1];
      return "[^" . $count . "]";
    },
    $text
  );
  $text .= $notes;

  // Code
  // SPIP     : <code>…</code>
  // Kramdown : ```…```
  $text = preg_replace("/<\/?code>/u", "```", $text);

  // Citation
  // SPIP     : <quote>…</quote>
  // Kramdown : >
  $text = preg_replace("/<quote>/u", "☞", $text);
  $text = preg_replace("/<\/quote>/u", "☜", $text);
  $text = preg_replace_callback(
    "/☞([^☜]+)☜/u",
    function($match) {
      return "> ".preg_replace("/\n/u", "\n> ", $match[1]);
    },
    $text
  );

  // Vidéos Youtube
  // SPIP / HTML             : <iframe src="https://www.youtube.com/embed/…"></iframe>
  // Jekyll Youtube Lazyload : {% youtube … %} cf https://github.com/erossignon/jekyll-youtube-lazyloading
  $text = preg_replace("/<iframe width=\"[0-9]+\" height=\"[0-9]+\" src=\"https:\/\/www\.youtube\.com\/embed\/([^\"]+)\" frameborder=\"0\" allowfullscreen><\/iframe>/u", "{% youtube $1 %}", $text);

  return $text;
}
