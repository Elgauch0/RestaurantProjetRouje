<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\RestaurantSettings;
use App\Repository\BookingRepository;

class BookingManager
{


    public function __construct(

        private readonly BookingRepository $bookingRepository,
    ) {}

    // src/Service/BookingManager.php

    public function getBookingError(RestaurantSettings $settings, Booking $booking): ?string
    {
        $datetime = $booking->getDatetime();

        // 1. Vérification du jour
        if (!$settings->isDayOpen($datetime)) {
            return "Le restaurant est fermé ce jour-là.";
        }

        // 2. Vérification du service
        if (!$settings->isServiceOpen($datetime)) {
            return "Nous sommes fermés à cette heure-là. Veuillez choisir un créneau valide.";
        }

        // 3. Vérification de la capacité
        $interval = $settings->getServiceInterval($datetime);
        $bookingsCount = $this->bookingRepository->getCountConvivesByInterval($interval['start'], $interval['end']);

        if (($bookingsCount + $booking->getGuestCount()) > $settings->getMaxConvives()) {
            return "Le restaurant est complet pour ce service. Il reste " . ($settings->getMaxConvives() - $bookingsCount) . " places.";
        }

        return null; // Tout est OK !
    }
}
