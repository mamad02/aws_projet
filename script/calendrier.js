
var time = new Date().getTime()-((new Date().getDay()-1)*(3600*24*1000));
var tab_html = [];

display_calendrier(time);
display_events(time);

function display_calendrier(time) {

    document.querySelector("#calendrier").innerHTML = "";
     
    var div = document.querySelector("#calendrier");
    var tab = document.createElement("table");
    tab.className = "table table-bordered";
    var h= 0;
    var jours = new Array ("Lun","Mar","Mer","Jeu","Ven","Sam","Dim");
    var mois = new Array ("Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre");
    
    var date = new Date();
    
     
     var decalage = document.createElement("th");
     decalage.innerHTML += "";
     tab.appendChild(decalage);
    
    var last_monday = new Date().getTime()-((new Date().getDay()-1)*(3600*24*1000));
    var week = new Array();
    
    for (var j =0;j<7;j++)
    {
        var th_jour = document.createElement("th");
        
        week[j] = inverser_date(new Date(this.time+(3600*24*1000*j)));
        
        th_jour.innerHTML += jours[j] + " " + display_date(new Date(this.time+(3600*24*1000*j)));
          tab.appendChild(th_jour);
    }
    
    for (var i = 0; i < 48; i++) {
       
        var min;
       
        var tr = document.createElement("tr");
     
        var th_heure = document.createElement("th");
        
        if(i%2 ==0){
             
            min = "00"; 
            if (h < 10) {
                th_heure.innerHTML += "0" + h + ":" + "00";
            } else {
                th_heure.innerHTML += h + ":" + "00";
            }
            
       } else min = 30;
       
        tr.appendChild(th_heure);
          
        tab.appendChild(tr);
        tab_html[i] = [];
        
        for (var j = 0; j < 7; j++) {
            
            var td = document.createElement("td");
           
            td.dataset['x'] = i;
            td.dataset['y'] = j;
            td.dataset['event']=0;
            if(h<10) {
                td.dataset['heure'] = "0" + h;
                td.dataset['min'] = min;
                td.dataset['date'] = week[j] + " 0"  + h + ":" + min + ":00";
            } else {
                td.dataset['heure'] = h;
                td.dataset['min'] = min;
                td.dataset['date'] = week[j] + " " + h + ":" + min + ":00";
            }
            td.dataset['jour'] = week[j];
           
            tab_html[i][j] = td;
            tr.appendChild(td);
        }
        if(i%2 == 1) {
            h++;   
        }
    }
    div.appendChild(tab);
}

 function inverser_date(date) {
     
     var year = date.getFullYear();
     var month = date.getMonth()+1;
     var day = date.getDate();
     
     if(month < 10) month = '0' + month;
     if(day < 10) day = '0' + day;
     
     return year + "-" + month + "-" + day;
 }
 
 function display_date(date) {
     
     var month = date.getMonth()+1;
     var day = date.getDate();
     
     if(month < 10) month = '0' + month;
     if(day < 10) day = '0' + day;
     return day + "/" + month;
 }
 
 function heure_fin() {
  
    var date_debut = document.querySelector("#date_debut").value;
    var select = document.querySelector("#date_fin");
  
    if (date_debut != "") {
        for (var i = date_debut; i < 24; i++) { 
      
            var option = document.createElement("option");
            option.innerHTML = i;
            option.value = i;
            select.appendChild(option);
         
        }
    }
     
 }
 
document.getElementById("calendrier").onmousedown=function(e){
    
    var cellule=e.target;
    var date_debut = cellule.dataset['date'];

        var x=cellule.dataset['x'];
        var y=cellule.dataset['y'];
        var min=cellule.dataset['min'];
        var h=parseInt(cellule.dataset['heure']);
        var jour=cellule.dataset['jour'];
    
     document.getElementById("date_debut").value=date_debut;
     document.getElementById("jour").value=jour;
     
     var select = document.getElementById("heure_fin");
     select.innerHTML = "";

    if (min == 30) {
        h = h + 1;
    }
    

     for (var i = h; i < 24; i++) { 
      
            var option = document.createElement("option");
            option.innerHTML = i;
            option.value = i;
            select.appendChild(option);
         
    }
     
    if (document.getElementById("date_debut").value != "undefined" && document.getElementById("online").value != "" && cellule.dataset['event'] == 0 ) {
        $('.modal').modal('show');
    }
}

document.getElementById("next").onclick=function(){
    time+=3600*24*7*1000;

    display_calendrier(time);
    display_events(time);

}


document.getElementById("previous").onclick=function(){
    time-=3600*24*7*1000;

    display_calendrier(time);
    display_events(time);

}

function display_events(time){
    var resultat;
    var date_debut=new Date(time);
    var date_fin=new Date(time + (3600 * 24 * 7 * 1000 ) );
    var k = 0;
    var en_cours = 0;
    var i_debut;
    var j_debut;
    
    var xhr=new XMLHttpRequest();
    xhr.open("POST","/events");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload=function(){

        resultat=JSON.parse(xhr.responseText) ;
        var nb_resultat=resultat.length ;

        for( var i=0 ; i<7 ; i++ ){
            for ( var j = 0; j < 47; j++ ) {
                if(k == nb_resultat) return 1;
         
                if(tab_html[j][i].dataset['date'] == resultat[k].start_event && en_cours != 1) {
                    
                    tab_html[j][i].style.backgroundColor="rgb(33, 123, 123)";
                    tab_html[j][i].dataset['event']=1;
                    tab_html[j][i].innerHTML+= '<br><b>Titre: '+resultat[k].title_event+'</b> <br> <b>Description: </b>'+resultat[k].desc_event+'<br> <b>Créé par: </b>'+resultat[k].login_user;
                    
                    if (document.getElementById("offline") == null) {
                        
                        if(document.getElementById("id_user").value == resultat[k].id_user) {
                            tab_html[j][i].innerHTML+= '<br> <button id="deleteButton" onclick="delete_event('+ resultat[k].id_event +')">X</button>';
                            tab_html[j][i].innerHTML+= "<br> <form method='POST' action='/update'> <input type='hidden' id='id_event' name='id_event' value='" + resultat[k].id_event + "'/> <input type='submit' value='Modifier'> </form>";
                        }
                    }
                    
                    en_cours = 1;
                    j_debut = j;
                    i_debut = i;
                    
                } else if (en_cours == 1) {
                    tab_html[j][i].parentNode.removeChild(tab_html[j][i]);
                }
                
                if(tab_html[j+1][i].dataset['date'] == resultat[k].end_event && en_cours == 1 ) {
                    tab_html[j_debut][i_debut].setAttribute('rowspan',j - j_debut +1);
                    en_cours = 0;
                    k++;
                }
           
            }
        }

    };

    xhr.send("date_start="+inverser_date(date_debut)+" 00:00:00&date_end="+inverser_date(date_fin)+" 00:00:00");
}

function delete_event(id_event) {
    
    var r = confirm("Voulez-vous vraiment supprimer l'évenement ?");
    if (r == true) {
    
        var xhr=new XMLHttpRequest();
        xhr.open("POST","/delete");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onload=function(){
            
            display_calendrier(time);
            display_events(time);
        }
        xhr.send("id_event="+id_event);
    } else {
    }
}