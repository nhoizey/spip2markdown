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
  $text = spip2markdown_documents($text);

  // ne devrait pas être là, trop spécifique à un usage
  $text = spip2markdown_youtube($text);
  $text = spip2markdown_twitter($text);

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

function spip2markdown_documents($text) {
  // SPIP     : <doc…|…> ou <img…|…> ou <emb…|…>
  // Kramdown :
  // - <figure>{% picture blah-blah.jpg %}<figcaption>blah…</figcaption></figure> pour les images
  //   cf https://github.com/gettalong/kramdown/issues/48#issuecomment-16698925
  // - ou un lien
  $text = preg_replace_callback(
    "/<(doc|img|emb)([0-9]+)[^0-9>]*>/u",
    function($match) {
      $doc_id = $match[2];
      $doc = sql_fetsel("titre, descriptif, fichier, mode, media", "spip_documents", "id_document=$doc_id");
      if ($doc['media'] == "image") {
        $doc_str = "\n<figure>\n  {% picture " . basename($doc['fichier']) . " %}";
        if (strlen($doc['titre']) > 0 || strlen($doc['descriptif']) > 0) {
          $doc_str .= "\n  <figcaption>";
          if (strlen($doc['titre']) > 0) {
            $doc_str .= "\n    " . spip2markdown($doc['titre']);
          }
          if (strlen($doc['descriptif']) > 0) {
            $doc_str .= "\n\n    " . spip2markdown($doc['descriptif']);
          }
          $doc_str .= "\n  </figcaption>";
        }
        $doc_str .= "\n</figure>";
      } else {
        $doc_str = "[" . $doc['titre'] . "](" . basename($doc['fichier']) . ")";
      }
      return $doc_str;
    },
    $text
  );

  return $text;
}

function spip2markdown_youtube($text) {
  // SPIP / HTML             : <iframe src="https://www.youtube.com/embed/…"></iframe>
  // Jekyll Youtube Lazyload : {% youtube … %} cf https://github.com/erossignon/jekyll-youtube-lazyloading
  $text = preg_replace("/<iframe [^>]*src=\"https?:\/\/www\.youtube\.com\/embed\/([^\"]+)\"[^>]*><\/iframe>/u", "{% youtube $1 %}", $text);

  return $text;
}

function spip2markdown_twitter($text) {
  // SPIP / HTML    : <blockquote class="twitter-tweet">…<a href="https://twitter.com/mariejulien/statuses/354870574595584000">…</a></blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
  // Jekyll Twitter : {% twitter oembed … %} cf https://github.com/rob-murray/jekyll-twitter-plugin
  $text = preg_replace_callback(
    "/<blockquote class=\"twitter-tweet\">(.*)(?<!<\/blockquote>)<\/blockquote>/u",
    function($match) {
      return preg_replace("/.*<a href=\"(https:\/\/twitter.com\/[^\/]+\/statuses\/[^\"]+)\">[^<]+<\/a>$/u", "{% twitter oembed $1 %}", $match[1]);
    },
    $text
  );
  $text = str_replace("<script async src=\"//platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>", "", $text);

  return $text;
}
