async function checkWeather(weather) {
    let data;
    if(navigator.onLine){
        const response = await fetch(`connection.php?q=${weather}`);
     if(!response.ok) {
        throw new Error ("Network response was not ok");
     }
     data = await response.json();
     localStorage.setItem(weather,JSON.stringify(data)); 

    }else{
        data= JSON.parse(localStorage.getItem(weather));
    }
    console.log(data);
    document.getElementById('Humidity_data').innerHTML = `${data[0].humidity}%`;
    document.getElementById('Pressure_data').innerHTML = `${data[0].pressure}hPa`;
    document.getElementById('Speed_data').innerHTML = `${data[0].wind}m/s`;
    document.getElementById('temp').innerHTML = `${data[0].temperature}°C` ;
    document.getElementById('city').innerHTML = data[0].city;
    document.getElementById('w_icon').src = data[0].weatherIcon;
    document.getElementById('w_desc').innerHTML= `${data[0].tempCondition}`.toUpperCase() ;
    document.querySelector("#Time").innerHTML =data[0].date;   
}

document.getElementById('searchButton').addEventListener('click', function () {
let x = document.getElementById('searchBox').value;
checkWeather(x);
})
checkWeather("Manchester");







