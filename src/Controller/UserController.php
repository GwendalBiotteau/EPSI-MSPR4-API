<?php

namespace App\Controller;

use App\Entity\User;
use Endroid\QrCode\QrCode;
use OpenApi\Attributes as OA;
use App\Service\MailerService;
use Endroid\QrCode\Writer\PngWriter;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
     * @param UserPasswordHasherInterface $userPasswordHasher
     *
     * @return JsonResponse
     */
    #[Route('/admin/register', name: 'api_register', methods: ['POST'])]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'email',
                    example: 'john.doe@email.com',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'firstName',
                    example: 'John',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    example: 'Doe',
                    type: 'string',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Retailer successfully created',
    )]
    #[OA\Response(
        response: 400,
        description: 'Missing required data in request body',
    )]
    #[OA\Response(
        response: 405,
        description: 'A user already exists with this email address',
    )]
    #[OA\Tag(name: 'Admin')]
    #[Security(name: 'Bearer')]
    public function register(Request $request, EntityManagerInterface $entityManager, MailerService $mailer, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
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
            ->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userPassword
                )
            )
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
     * Send a new QRCode to a retailer
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MailerService $mailer
     * @param UserPasswordHasherInterface $userPasswordHasher
     *
     * @return JsonResponse
     */
    #[Route('/admin/generateQRCode', name: 'api_new_qr_code', methods: ['POST'])]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'email',
                    example: 'john.doe@email.com',
                    type: 'string',
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'QRCode successfully resent',
    )]
    #[OA\Response(
        response: 400,
        description: 'Missing required data in request body',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Tag(name: 'Admin')]
    #[Security(name: 'Bearer')]
    public function newQRCode(Request $request, EntityManagerInterface $entityManager, MailerService $mailer, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        // Fetch data from request
        $data = json_decode($request->getContent());

        // Check required data
        if (empty($data->email)) {
            throw new HttpException(
                statusCode: 400,
                message: 'Missing required data in request body',
            );
        }

        // Check if user already exists
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data->email]);
        if (empty($user)) {
            throw new HttpException(
                statusCode: 404,
                message: 'User not found',
            );
        }

        // Generate a new password for the user
        $userPassword = bin2hex(random_bytes(10));
        $encryptedPassword = $this->encryptPassword($userPassword);

        // Hash password and update user
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $userPassword
            )
        );
        $entityManager->flush();

        // Create QRCode with user data
        $writer = new PngWriter();
        $qrCode = QrCode::create('{"email": "' . $data->email . '", "password": "' . $encryptedPassword . '"}');
        $result = $writer->write($qrCode);

        // Send email to user with QRCode
        $mailer->sendEmail(
            to: $data->email,
            subject: 'Ton nouveau QRCode',
            text: 'Ton ancien QRCode Paye ton Kawa a expiré et tu as fait une demande de renouvellement, tu trouveras en pièce jointe ton nouveau QRCode !',
            attachment: $result->getString(),
            attachmentName: 'QRCode.png'
        );

        return new JsonResponse(data: 'QRCode successfully resent', status: 200);
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
