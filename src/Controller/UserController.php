<?php

namespace App\Controller;

use App\Entity\User;
use Endroid\QrCode\QrCode;
use App\Service\MailerService;
use Endroid\QrCode\Writer\PngWriter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * User Controller to manage user registration process
 */
class UserController extends AbstractController
{
    /**
     * Retailers registration
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MailerService $mailer
     *
     * @return JsonResponse
     */
    #[Route('/register', name: 'api_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, MailerService $mailer): JsonResponse
    {
        // Fetch data from request
        $data = json_decode($request->getContent());

        // Check required data
        if (empty($data->email) || empty($data->firstName) || empty($data->lastName)) {
            throw new HttpException(
                statusCode: 400,
                message: 'Missing required data in request body',
            );
        }

        // Check if user already exists
        if ($entityManager->getRepository(User::class)->findOneBy(['email' => $data->email])) {
            throw new HttpException(
                statusCode: 405,
                message: 'A user already exists with this email address',
            );
        }

        // Generate a password for the new user
        $userPassword = bin2hex(random_bytes(10));
        $encryptedPassword = $this->encryptPassword($userPassword); 

        // Create new user
        $user = new User();
        $user->setEmail($data->email)
            ->setFirstName($data->firstName)
            ->setLastName($data->lastName)
            ->setPassword($userPassword)
            ->setRoles(['ROLE_RETAILER']);

        // Persist user
        $entityManager->persist($user);
        $entityManager->flush();

        // Create QRCode with user data
        $writer = new PngWriter();
        $qrCode = QrCode::create('{"email": "' . $data->email . '", "password": "' . $encryptedPassword . '"}');
        $result = $writer->write($qrCode);

        // Send email to user with QRCode
        $mailer->sendEmail(
            to: $data->email,
            subject: 'Bienvenue sur Paye ton Kawa !',
            text: 'Félicitation, tu as été inscrit sur Paye ton Kawa en tant que revendeur ! Tu trouveras en pièce jointe le QRCode qui permettra de te connecter sur l\'application !',
            attachment: $result->getString(),
            attachmentName: 'QRCode.png'
        );

        return new JsonResponse(data: 'Retailer successfully created', status: 201);
    }

    /**
     * Encrypt a password to be send externally
     * ! Temporary solution, need to be upgraded with more security
     *
     * @param string $password
     *
     * @return string
     */
    protected function encryptPassword(string $password): string
    {
        // Encode password to base64
        $encryptedPassword64 = base64_encode($password);

        return $encryptedPassword64;
    }
}
