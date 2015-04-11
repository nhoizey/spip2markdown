# spip2markdown

Convertisseur des raccourcis typographiques SPIP vers Markdown.

## Mode d'emploi

1. Installez et activez le plugin
2. Chargez la page ```/?page=article2markdown&id_article=…``` avec l'id d'article de votre choix
3. Voilà le contenu de l'article proposé en Markdown — ou plutôt [Kramdown](http://kramdown.gettalong.org/syntax.html), une variante —, avec les méta données présentées dans le [YAML Front Matter](http://jekyllrb.com/docs/frontmatter/)
4. Vous pouvez aussi utiliser le filtre ```spip2markdown``` dans vos propres squelettes

## Éléments de syntaxe gérés

- intertitres
- gras
- italique
- liens
- notes de bas de page
- code
- citations

## Licence

MIT

## To do

- compléter les éléments de syntaxe manquants (images, documents, tableaux, etc.)
- faire des squelettes pour les autres types de contenus (rubriques, brèves, etc.)
- rendre paramétrables certaines fonctionnalités
 - forçage du téléchargement (avec nom de fichier prédéfini) vs affichage dans le navigateur
 - présence du YAML Front Matter
 - syntaxe Kramdown ou autre
 - syntaxe très spécifique des vidéos Youtube *lazyloadées* avec [Jekyll Youtube Lazyloading](https://github.com/erossignon/jekyll-youtube-lazyloading)
- proposer de générer un zip avec le contenu en Markdown/Kramdown et les ressources (images, documents, etc.)
- découper le filtre en transformations unitaires et écrire des tests unitaires correspondants
- migrer en textwheel ?

