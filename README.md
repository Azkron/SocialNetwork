28/05/2017      
G10: Hugo Barbachano & Tidiane Toure

I. Correction de la seconde itération

Nous avons rectifié notre second travail en suivant la série de recommandations reçue le 21/04/2017:

    FONCTIONNALITES:

    - View Event (update) => devrait pouvoir afficher le détail d'un évènement partagé 

    CODE SOURCE:
    
    - Qualité modèle        =>  * Calendar::get_calendars($user) : devrait être méthode 
                                  d'instance de User
                                * Calendar::has_events() : devrait être méthode d'instance
                                * idem pour get_writable_calendars(), get_events_in_week(), 
                                  get_shared_users(),get_not_shared_users(), ...

    - Qualités controllers  =>  vérifier que l'action demandée est autorisée pour 
                                l'utilisateur courant : exemple: edit_share(), 
                                delete_share(), create_share(), edit(), ... Idem partout !

    - Qualité vues          =>  View::print_errors() => pas modifier fwk mais faire votre 
                                propre classe qui hérite de View

    DIVERS :

Pour ce qui concerne les contraintes et les règles métiers, nous avons fait en sorte que toutes nos pages sont conformes au standard W3C HTML5 (vérifié à l'aide du validateur W3C), with the exception of the display of the events in the color of the calendar, we had to add the color="#.." in the html directly because we use the color palette.

II. Résume de l'itération finale

    Nous avons implémenté toutes les nouvelles fonctionnalités ainsi que les validations des formulaires avec le plugin jQuery Validation vu au cours.    

    Enfin, nous avons ajouté plusieurs fonctionnalités qui se remarque sur la page My Planning avec l'affiche du calendrier à l'aide du plugin jQuery FullCalendar:
        - Créer des événements via un click sur une partie vide du calendrier;
        - Editer des événements via un click sur l'événement désiré
        - Event drag´n´drop
        - Event resize
        - Dynamic calendar hidding




We created 2 users for testing; 
Tid and Hugo
The password for both users is EPFC2017.  <-the last char of the password is a dot (.)