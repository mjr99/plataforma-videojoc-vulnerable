// --------- Classe Base ---------
class Entitat {
  constructor(posicio = {x: 0, y:0}, ample = 50, alt = 50) {
    this.x = posicio.x;
    this.y = posicio.y;
    this.ample = ample;
    this.alt = alt;
    //Crear l'element HTML
    this.elementHTML = document.createElement("div");
    this.elementHTML.style.left = this.x + "px";
    this.elementHTML.style.top = this.y + "px";
    this.elementHTML.style.width = this.ample + "px";
    this.elementHTML.style.height = this.alt + "px";
  }

  // Modifica la posició de l'element a la pantalla
  dibuixar() {
    this.elementHTML.style.left = this.x + "px";
    this.elementHTML.style.top = this.y + "px";
  }

  moure() {
    // Implementar la lògica de moviment
  }
}

// (classes.js) Herència de Classes i Polimorfisme

class Jugador extends Entitat {
  constructor(nom, vides, velocitat, posicio, ample, alt) {
    super(posicio, ample, alt);
    this.nom = nom;
    this.vides = vides;
    this.velocitat = velocitat;
    this.punts = 0;
    this.derribats = 0;
    this.elementHTML.classList.add("nau", "jugador");
  }

  moure() {
    if (this.y < 0) {
      this.y = 0;
    } else if (this.y + this.alt > pantallaAlt) {
      this.y = pantallaAlt - this.alt;
    }
  }
}

class Enemic extends Entitat {
  constructor(velocitat, posicio, ample, alt) {
    super(posicio, ample, alt);
    this.velocitat = velocitat;
    this.elementHTML.classList.add("nau", "enemic");
  }

  moure() {
    this.x -= this.velocitat;
    if (this.x < -this.ample) {
      this.x = pantallaAmple + this.ample;
    }
  }
}

class Asteroide extends Entitat {
  constructor(velocitat, posicio, ample = 5, alt = 5) {
    super(posicio, ample, alt);
    this.velocitat = velocitat;
    this.elementHTML.classList.add("asteroide");
  }

  moure() {
    this.x -= this.velocitat;
    if (this.x < -this.ample) {
      this.x = pantallaAmple + this.ample;
    }
  }
  

}

class Laser extends Entitat {
  constructor(posicio, ample = 10, alt = 4, velocitat = 15) {
    super(posicio, ample, alt);
    this.velocitat = velocitat;
    this.elementHTML.classList.add("laser");
  }

  moure() {
    this.x += this.velocitat;
    if (this.x > pantallaAmple) {
      this.elementHTML.remove();
    }
  }
}
class FinalBoss {
  constructor() {
    this.x = pantallaAmple - 220;
    this.y = 100;
    this.ample = 200;
    this.alt = 200;
    this.velocitat = 2;
    this.vida = 10;
    this.elementHTML = document.createElement("div");
    this.elementHTML.classList.add("finalboss");
    this.elementHTML.style.position = "absolute";
    this.elementHTML.style.width = "200px";
    this.elementHTML.style.height = "200px";
    this.elementHTML.style.backgroundImage = 'url("/img/finalboss.png")';
    this.elementHTML.style.backgroundSize = "cover";
    pantalla.append(this.elementHTML);
  }

  dibuixar() {
    this.elementHTML.style.left = `${this.x}px`;
    this.elementHTML.style.top = `${this.y}px`;
  }

  moure() {
    this.y += this.velocitat;
    if (this.y <= 0 || this.y + this.alt >= pantallaAlt) {
      this.velocitat *= -1;
    }
  }

  disparar() {
    const laser = new LaserBoss({ x: this.x, y: this.y + this.alt / 2 - 5 });
    vectorLasersBoss.push(laser);
    pantalla.append(laser.elementHTML);
  }
}
class LaserBoss {
  constructor(posicio) {
    this.x = posicio.x;
    this.y = posicio.y;
    this.velocitat = 5;
    this.ample = 10;
    this.alt = 10;
    this.elementHTML = document.createElement("div");
    this.elementHTML.classList.add("laserboss");
    this.elementHTML.style.position = "absolute";
    this.elementHTML.style.width = "10px";
    this.elementHTML.style.height = "10px";
    this.elementHTML.style.backgroundColor = "purple";
    pantalla.append(this.elementHTML);
  }

  dibuixar() {
    this.elementHTML.style.left = `${this.x}px`;
    this.elementHTML.style.top = `${this.y}px`;
  }

  moure() {
    this.x -= this.velocitat;
  }
}
