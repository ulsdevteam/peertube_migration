# peertube_migration
Peertube migration API for moving Peertube content to Drupal

## thumbnail migration

uses two migration yml files in that order
  - migrate_plus.migration.peertube_thumbnail.yml
  - migrate_plus.migration.peertube_images_to_media.yml

the first migration creates the file entity and places it into the drupal public directory, and the second migration creats an image media entity for that file and adds a relationship to the original remote video the thumbnail is referencing

## caption migration

uses two migration yml files in that order
  - migrate_plus.migration.peertube_video.yml
  - migrate_plus.migration.peertube_files_to_media.yml

the first migration creates the file entity and places it into the drupal public directory, and the second migration creats an caption file media entity for that file and adds a relationship to the original remote video the caption file is referencing

## migration flow for thumbnail 

### first half:

- first half of the migration pulls the content from peertube and places it in drupal's public directory
- source plugin: url
    - uses JSON API to pull all remote video's with their neccessary fields
    - field_media_oembed_video is the embed url of the remote video
- process plugins:
    - peertube_jpg with source: field_media_oembed_video
        - CUSTOM PLUGIN (/src/Plugin/migrate/process/peertubeJPG.php) that calls the peertube API and pulls the thumbnail path
            - example: "thumbnailPath": "/lazy-static/thumbnails/3f8131d4-0673-43de-8b5a-89fa149ac655.jpg"
    - source URI contructed with the peertube base url and thumbnail path
    - destination URI created using the public drupal directory and the remote video name
    - uri*:
      - plugin: file_copy
      - copies the file from the source URI to the destination URI
     
  * uri is the only required process item for migrating file entities

  - destination is an 'entity:file'

