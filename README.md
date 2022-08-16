# wp-development
Entorn de desenvolupament per wordpress 📰 basat en imatges de docker 🐋

## Dependències
- [docker](https://www.docker.com/)
- [docker-compose](https://docs.docker.com/compose/)
- [node](https://nodejs.org/en/) i [npm](https://www.npmjs.com/)
- [composer](https://getcomposer.org/)

## Instruccions
1. ✍️ `$ ./boostrap_theme.sh` i ens demanarà quin nom li volem donar al nou tema que estem desenvolupant i ens configurarà la plantilla *underscores_* i l'arxiu YAML de docker-compose
2. ⏲️ `docker-compose up -d` per aixecar els contenidors de docker de wordpress i mariadb. Si és el primer cop que utilitzes les impatges, docker començarà una descarrega de les imàtges desde els repositoris remots que pot trigar uns minuts. Els següents cops el temps es reduirà a uns parell de segons.
3. 🚀 Obre el contingut del directori `src/` amb l'editor de codi que més tragradi, i comença a desenvolupar
