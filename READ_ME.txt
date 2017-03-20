20/03/2017      
G10: Hugo Barbachano & Tidiane Toure

I. Correction de la première itération



Nous avons rectifié notre premier travail en suivant la série de recommandations reçue le 17/03/2017:

    FONCTIONNALITES:

    - Sign Up => deux users avec la même adressse mail (CORRECTED)

    - Delete calendar => msg de conf disant qu'il y a des events vides 

    - Create calendar => * quand on crée un event on est obligé d'encoder les heures même si whole day 
                         * start = finish pas possible (même en whole)

    - View planning => semaine vide : afficher les jours

    CODE SOURCE:
    
    - Qualité modèle        =>  * contruct calendar : pas besoin de return true
                                * calendar : tout en statique
                                * event: update, delete, validate en statique

    - Qualités controllers  =>  * méthodes internes à mettre en private
                                * ControllerEvent: calendars_exist à transférer dans Model
                                * ControllerCalendar: confirm_delete : sécurité

    - Qualité vues          =>  errors.php ? (pas utilisé)

    DIVERS :

    Base de données pas importable


II. Résume de la seconde itération

    Nous avons implémenté toutes les nouvelles fonctionnalités et les fonctionnalités mises à jour
mentionnés dans l'énoncé de l'itération 2.

    Pour ce qui concerne les contraintes et les règles métiers, nous avons fait en sorte que toutes 
nos pages sont conformes au standard W3C HTML5 (vérifié à l'aide du validateur W3C), with the exception of the 
display of the events in the color of the calendar, we had to add the color="#.." in the html directly because we 
use the color palette.

    We have decided to set the event hour to 00:00 if none is given by default for the comfort of the user.

    Enfin, nous avons ajouté une fonctionnalié qui se remarque sur la page My Calendars:
        - une précision sur les calendriers partagés :  on peut voir le pseudo du propriétaire





I created test 2 users; 
Tid and Hugo
The password for both users is EPFC2017.  <-the last char of the password is a dot (.)