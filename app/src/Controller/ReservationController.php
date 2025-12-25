<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\BookingType;
use App\Entity\Booking;
use App\Entity\RestaurantSettings;
use App\Repository\RestaurantSettingsRepository;
use App\Service\BookingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ReservationController extends AbstractController
{


    public  function __construct(
        private readonly EntityManagerInterface $em,
    ) {}



    #[Route('/reservation', name: 'app_reservation', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, RestaurantSettingsRepository $restaurantSettingsRepository, BookingManager $bookingManager): Response

    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $Booking = new Booking();
        $Booking->setAllergies($user->getAllergies());
        $Booking->setGuestCount($user->getGuestCount());
        $Booking->setClient($user);

        // Default reservation date to tomorrow at 7 PM
        $Booking->setDatetime((new \DateTimeImmutable())->modify('+1 day')->setTime(19, 0));

        $BookingForm = $this->createForm(BookingType::class, $Booking);

        $BookingForm->handleRequest($request);
        if ($BookingForm->isSubmitted() && $BookingForm->isValid()) {

            # Get restaurant settings
            $settings = $restaurantSettingsRepository->findOneBy([]) ?? new RestaurantSettings();

            $errors = $bookingManager->getBookingError($settings, $Booking);
            if ($errors) {
                $this->addFlash('error', $errors);
                return $this->redirectToRoute('app_reservation');
            }

            $this->em->persist($Booking);
            $this->em->flush();
            $this->addFlash('success', 'Votre réservation a bien été prise en compte !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('reservation/index.html.twig', [
            'bookingForm' => $BookingForm,
        ]);
    }
}
