jQuery(document).ready(function ($) {
    const tour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: {
                enabled: true
            },
            classes: 'class-1 class-2',
            scrollTo: { behavior: 'smooth', block: 'center' }
        }
    });
    
    tour.addStep({
        title: 'Creating a site Tour',
        text: 'Creating a site tour',
        attachTo: {
            element: '#menu-item-648',
            on: 'bottom'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                classes: 'shepherd-button-secondary',
                text: 'Back'
            },
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'creating'
    });
    
    tour.start();
});



