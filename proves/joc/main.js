// Variables de pantalla
const pantallaAmple = window.innerWidth;
const pantallaAlt = window.innerHeight;
const fotogrames = 1000 / 60;

// Variables de partida
let nivell = 1;
let finalBoss = null;
const vectorAsteroides = [];
const vectorLasers = [];
const vectorLasersBoss = [];
const maxAsteroides = 20;

const pantalla = document.querySelector("#pantalla");
const infoPartida = document.querySelector("#infoPartida");

// Jugador
const jugador = new Jugador("Pepito", 3, 10, {x: 100, y: 300}, 150, 100);
pantalla.append(jugador.elementHTML);

// Asteroides inicials
for (let i = 0; i < maxAsteroides; i++) {
  let posX = Math.floor(Math.random() * pantallaAmple - 3);
  let posY = Math.floor(Math.random() * pantallaAlt - 3);
  let velocitat = Math.random() * 1.5 + 0.2;
  vectorAsteroides.push(new Asteroide(velocitat, {x: posX, y: posY}, 30, 30));
  pantalla.append(vectorAsteroides[i].elementHTML);
}

// Marcador
const elementNom = document.createElement("p");
const elementPunts = document.createElement("p");
const elementDerribats = document.createElement("p");
const elementVides = document.createElement("p");
elementNom.innerHTML = `Jugador: ${jugador.nom}`;
elementPunts.innerHTML = `Punts: ${jugador.punts}`;
elementDerribats.innerHTML = `Kills: ${jugador.derribats}`;
elementVides.innerHTML = `Vides: ${jugador.vides}`;
infoPartida.append(elementNom, elementPunts, elementDerribats, elementVides);

// Teclat
window.addEventListener("keydown", (event) => {
  switch(event.code) {
    case "ArrowUp":
      jugador.y -= jugador.velocitat;
      jugador.dibuixar();
      break;
    case "ArrowDown":
      jugador.y += jugador.velocitat;
      jugador.dibuixar();
      break;
    case "Space":
      const laser = new Laser(
        { x: jugador.x + jugador.ample, y: jugador.y + jugador.alt / 2 - 3 }
      );
      vectorLasers.push(laser);
      pantalla.append(laser.elementHTML);
      break;
  }
});

// Funció de col·lisió
function colisiona(a, b) {
  return (
    a.x < b.x + b.ample &&
    a.x + a.ample > b.x &&
    a.y < b.y + b.alt &&
    a.y + a.alt > b.y
  );
}

// Funció per passar de nivell
function passarDeNivell() {
  nivell += 1;
  console.log(`🚀 Passant al nivell ${nivell}`);

  vectorAsteroides.forEach(asteroide => {
    if (nivell === 2) {
      asteroide.elementHTML.style.backgroundImage = 'url("/img/fireball2.png")';
      asteroide.velocitat += 1;
      asteroide.ample = 50;
      asteroide.alt = 50;
      asteroide.elementHTML.style.width = "50px";
      asteroide.elementHTML.style.height = "50px";
    } else if (nivell === 3) {
      asteroide.elementHTML.style.backgroundImage = 'url("/img/misilalien.gif")';
      asteroide.velocitat += 1.2;
      asteroide.ample = 100;
      asteroide.alt = 100;
      asteroide.elementHTML.style.width = "100px";
      asteroide.elementHTML.style.height = "100px";
    }
  });

  const missatge = document.createElement("div");
  missatge.innerText = `Nivell ${nivell}`;
  missatge.style.position = "absolute";
  missatge.style.top = "50%";
  missatge.style.left = "50%";
  missatge.style.transform = "translate(-50%, -50%)";
  missatge.style.color = "white";
  missatge.style.fontSize = "40px";
  missatge.style.zIndex = "100";
  pantalla.append(missatge);
  setTimeout(() => missatge.remove(), 2000);
}

// Missatge de victòria
function mostrarVictoria() {
  const win = document.createElement("div");
  win.innerText = "🎉 YOU WIN 🎉";
  win.style.position = "absolute";
  win.style.top = "50%";
  win.style.left = "50%";
  win.style.transform = "translate(-50%, -50%)";
  win.style.color = "lime";
  win.style.fontSize = "60px";
  win.style.zIndex = "200";
  pantalla.append(win);
}

// Bucle del joc
setInterval(() => {
  jugador.dibuixar();
  jugador.moure();

  vectorAsteroides.forEach(asteroide => {
    asteroide.dibuixar();
    asteroide.moure();
  });

  // Regenerar asteroides si queden 10 o menys
  if (vectorAsteroides.length <= 10 && !finalBoss) {
    for (let i = 0; i < 5; i++) {
      let posX = pantallaAmple + Math.floor(Math.random() * 100);
      let posY = Math.floor(Math.random() * pantallaAlt - 3);
      let velocitat = Math.random() * 1.5 + 0.2;

      if (nivell === 3) {
        velocitat += 2;
        const nouAsteroide = new Asteroide(velocitat, {x: posX, y: posY}, 100, 100);
        nouAsteroide.elementHTML.style.backgroundImage = 'url("/img/misilalien.gif")';
        nouAsteroide.elementHTML.style.width = "100px";
        nouAsteroide.elementHTML.style.height = "100px";
        pantalla.append(nouAsteroide.elementHTML);
        vectorAsteroides.push(nouAsteroide);
      } else if (nivell === 2) {
        velocitat += 1;
        const nouAsteroide = new Asteroide(velocitat, {x: posX, y: posY}, 50, 50);
        nouAsteroide.elementHTML.style.backgroundImage = 'url("/img/fireball2.png")';
        nouAsteroide.elementHTML.style.width = "50px";
        nouAsteroide.elementHTML.style.height = "50px";
        pantalla.append(nouAsteroide.elementHTML);
        vectorAsteroides.push(nouAsteroide);
      } else {
        const nouAsteroide = new Asteroide(velocitat, {x: posX, y: posY}, 30, 30);
        pantalla.append(nouAsteroide.elementHTML);
        vectorAsteroides.push(nouAsteroide);
      }
    }
  }

  // Boss final
  if (finalBoss) {
    finalBoss.moure();
    finalBoss.dibuixar();
    if (Math.random() < 0.02) {
      finalBoss.disparar();
    }
  }

  // Làsers del boss
  for (let i = vectorLasersBoss.length - 1; i >= 0; i--) {
    const laser = vectorLasersBoss[i];
    laser.moure();
    laser.dibuixar();

    if (colisiona(laser, jugador)) {
      alert("Has sido destruido por el jefe final 💀");
      location.reload();
    }

    if (laser.x < 0) {
      laser.elementHTML.remove();
      vectorLasersBoss.splice(i, 1);
    }
  }

  // Col·lisions amb el jugador
  for (let i = vectorAsteroides.length - 1; i >= 0; i--) {
    const asteroide = vectorAsteroides[i];

    if (colisiona(jugador, asteroide)) {
      asteroide.elementHTML.remove();
      vectorAsteroides.splice(i, 1);

      jugador.vides -= 1;
      elementVides.innerHTML = `Vides: ${jugador.vides}`;

      jugador.elementHTML.style.filter = "brightness(0.5)";
      setTimeout(() => {
        jugador.elementHTML.style.filter = "none";
      }, 300);

      if (jugador.vides <= 0) {
        alert("Game Over!");
        location.reload();
      }
    }
  }

  // Làsers del jugador
  for (let i = vectorLasers.length - 1; i >= 0; i--) {
    const laser = vectorLasers[i];
    laser.dibuixar();
    laser.moure();

    // Colisión con el jefe final
    if (finalBoss && colisiona(laser, finalBoss)) {
      laser.elementHTML.remove();
      vectorLasers.splice(i, 1);
      finalBoss.vida -= 1;

      if (finalBoss.vida <= 0) {
        finalBoss.elementHTML.remove();
        finalBoss = null;
        mostrarVictoria();
      }

      continue;
    }

    // Colisión con asteroides
    for (let j = vectorAsteroides.length - 1; j >= 0; j--) {
      const asteroide = vectorAsteroides[j];

      if (colisiona(laser, asteroide)) {
        asteroide.elementHTML.remove();
        laser.elementHTML.remove();
        vectorAsteroides.splice(j, 1);
        vectorLasers.splice(i, 1);
        jugador.punts += 10;
        jugador.derribats += 1;
        elementPunts.innerHTML = `Punts: ${jugador.punts}`;
        elementDerribats.innerHTML = `Kills: ${jugador.derribats}`;

        if (jugador.derribats >= 10 && nivell === 1) {
          passarDeNivell();
        }
        if (jugador.derribats >= 25 && nivell === 2) {
          passarDeNivell();
        }
        if (jugador.derribats >= 40 && nivell === 3 && !finalBoss) {
          nivell += 1;
          finalBoss = new FinalBoss();
        }

        break;
      }
    }
  }
}, fotogrames);