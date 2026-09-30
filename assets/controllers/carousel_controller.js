import { Controller } from "@hotwired/stimulus";
import Splide from "@splidejs/splide";

export default class extends Controller {
    connect() {
        this.carousel = new Splide(this.element, {
            type: "loop",
            rewind: true,
            perPage: 1,
            gap: "1rem",
            arrows: false,
            pagination: true,
            autoplay: true,
            interval: 5000,
            speed: 600,
            i18n: {
                prev: "Précédent",
                next: "Suivant",
                first: "Première image",
                last: "Dernière image",
                slideX: "Aller à l’image %s",
                pageX: "Aller à la page %s",
                slideLabel: "%s sur %s",
                carousel: "carrousel",
                slide: "image",
                select: "Choisir une image à afficher",
            },
        });

        this.carousel.mount();
    }

    disconnect() {
        this.carousel?.destroy(true);
        this.carousel = null;
    }
}
