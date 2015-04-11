<?php
function spip2markdown($text) {
  $text = spip2markdown_intertitres($text);
  $text = spip2markdown_gras($text);
  $text = spip2markdown_italiques($text);
  $text = spip2markdown_liens($text);
  $text = spip2markdown_notes($text);
  $text = spip2markdown_codes($text);
  $text = spip2markdown_citations($text);
  $text = spip2markdown_listes_non_ordonnees($text);
  $text = spip2markdown_listes_ordonnees($text);

  // ne devrait pas être là, trop spécifique à un usage
  $text = spip2markdown_youtube($text);

  return $text;
}

function spip2markdown_intertitres($text) {
  // SPIP     : {{{…}}}
  // Kramdown : ## …
  $text = preg_replace("/(^|[^{]){{{([^}]+)}}}([^}]|$)/u", "$1\n## $2\n$3", $text);
  $text = preg_replace("/\n\n?(## [^\n]*)\n\n?/u", "\n$1\n", $text); // retrait des retours chariot en trop

  return $text;
}

function spip2markdown_gras($text) {
  // SPIP     : {{…}}
  // Kramdown : **…**
  $text = preg_replace("/(^|[^{]){{([^}]+)}}([^}]|$)/u", "$1**$2**$3", $text);

  return $text;
}

function spip2markdown_italiques($text) {
  // SPIP     : {…}
  // Kramdown : *…*
  $text = preg_replace("/(^|[^{]){([^}]+)}([^}]|$)/u", "$1*$2*$3", $text);

  return $text;
}

function spip2markdown_liens($text) {
  // SPIP     : [intitulé->lien]
  // Kramdown : [intitulé](lien)
  $text = preg_replace("/(^|[^\[])\[([^\]\[]+)->([^\]\[]+)\]([^\]]|$)/u", "$1[$2]($3)$4", $text);

  return $text;
}

function spip2markdown_notes($text) {
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

  return $text;
}

function spip2markdown_codes($text) {
  // SPIP     : <code>…</code>
  // Kramdown : ```…```
  $text = preg_replace("/<\/?code>/u", "```", $text);

  return $text;
}

function spip2markdown_citations($text) {
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

  return $text;
}

function spip2markdown_listes_non_ordonnees($text) {
  // SPIP     : - ou -* en premier niveau, -** pour second niveau, etc.
  // Kramdown : - avec indentations par multiples de 4 espaces
  $text = preg_replace("/(^|\n)-\*? /u", "\n$1- ", $text); // ajout d'un retour chariot pour séparer d'un éventuel élément non liste
  $text = preg_replace("/\n- ([^\n]*)\n\n- /u", "\n- $1\n- ", $text); // retrait des retours chariot en trop
  $text = preg_replace("/\n- ([^\n]*)\n\n- /u", "\n- $1\n- ", $text); // retrait des retours chariot en trop
  $text = preg_replace("/(^|\n)-\*\* /u", "$1    - ", $text);
  $text = preg_replace("/(^|\n)-\*\*\* /u", "$1        - ", $text);
  $text = preg_replace("/(^|\n)-\*\*\*\* /u", "$1            - ", $text);

  return $text;
}

function spip2markdown_listes_ordonnees($text) {
  // SPIP     : -# en premier niveau, -## pour second niveau, etc.
  // Kramdown : 1. avec indentations par multiples de 4 espaces
  $text = preg_replace("/(^|\n)-# /u", "\n${1}1. ", $text);
  $text = preg_replace("/\n1\. ([^\n]*)\n\n1\. /u", "\n1. $1\n1. ", $text); // retrait des retours chariot en trop
  $text = preg_replace("/\n1\. ([^\n]*)\n\n1\. /u", "\n1. $1\n1. ", $text); // retrait des retours chariot en trop
  $text = preg_replace("/(^|\n)-## /u", "$1    1. ", $text);
  $text = preg_replace("/(^|\n)-### /u", "$1        1. ", $text);
  $text = preg_replace("/(^|\n)-#### /u", "$1            1. ", $text);

  return $text;
}

function spip2markdown_youtube($text) {
  // SPIP / HTML             : <iframe src="https://www.youtube.com/embed/…"></iframe>
  // Jekyll Youtube Lazyload : {% youtube … %} cf https://github.com/erossignon/jekyll-youtube-lazyloading
  $text = preg_replace("/<iframe width=\"[0-9]+\" height=\"[0-9]+\" src=\"https:\/\/www\.youtube\.com\/embed\/([^\"]+)\" frameborder=\"0\" allowfullscreen><\/iframe>/u", "{% youtube $1 %}", $text);

  return $text;
}
