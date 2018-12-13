<?php

namespace App\Controller;

use App\Utils\Alexa;
use App\Utils\AlexaException;
use App\Utils\Api;
use App\Utils\ApiException;
use primus852\ShortResponse\ShortResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    /**
     * @Route("/alexa/v1", name="alexaV1", methods={"POST"})
     * @param Request $request
     * @param KernelInterface $kernel
     * @return JsonResponse
     */
    public function alexa(Request $request, KernelInterface $kernel)
    {

        /**
         * Get the Request Body
         */
        $alexa_request_body = json_decode($request->getContent(), true);

        /**
         * Save the Request
         * @todo: Remove saving, this is only for debugging
         */
        try {
            $now = new \DateTime();
        } catch (\Exception $e) {
            return ShortResponse::exception('Invalid Date', $e->getMessage());
        }
        $fs = new Filesystem();
        /**
         * Create the tmp folder
         */
        $tmp_dir = $kernel->getProjectDir() . '/var/tmp';
        $file = 'req_' . $now->format('Y-m-d-H-i-s') . '.log';
        $path = $tmp_dir.'/'.$file;
        if (!$fs->exists($tmp_dir)) {
            $fs->mkdir($tmp_dir);
            $fs->chown($tmp_dir, 'www-data', true);
        }

        /**
         * Save the file
         */
        $fs->dumpFile($path, json_encode($alexa_request_body));

        /**
         * Create a new Alexa Class
         */
        try {
            $alexa = new Alexa(getenv('ALEXA_SKILL_ID'));
        } catch (AlexaException $e) {
            $fs->appendToFile($path,'\nAlexa Init failed: '.$e->getMessage().'\n');
            return ShortResponse::exception('Alexa initialization failed', $e->getMessage());
        }

        /**
         * Verify the Alexa Request
         */
        try {
            $alexa->verify($alexa_request_body);
        } catch (AlexaException $e) {
            $fs->appendToFile($path,'\nCould not verify Alexa Skill ID: '.$e->getMessage().'\n');
            return ShortResponse::error('Could not verify Alexa Skill ID', array(
                'body' => $alexa_request_body,
                'exception' => $e->getMessage(),
            ));
        }

        /**
         * For now we only save the request
         * @todo: change to sth. meaningful
         */
        $fs->appendToFile($path,'\nRequest saved\n');
        return ShortResponse::success('Request saved');
    }

    /**
     * @Route("/api/v1/{date}", name="apiV1", defaults={"date"="0"}, methods={"POST"})
     * @param Request $request
     * @param string $date
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request, string $date)
    {

        /**
         * Init the Api
         */
        try {
            $api = new Api($this->getDoctrine()->getManager(), $request);
        } catch (ApiException $e) {
            return ShortResponse::exception('Initialization failed, ' . $e->getMessage() . '');
        }

        /**
         * Get the endpoint by the sent date
         */
        $endpoint = $api->parse_date($date);
        if (!$endpoint) {
            return ShortResponse::error('Invalid Date');
        }

        /**
         * Do the Request
         */
        try {
            $data = $api->$endpoint($date);
        } catch (\Exception $e) {
            return ShortResponse::exception('Query failed, please try again', $e->getMessage());
        }

        /**
         * Return as JSON
         */
        return new JsonResponse($data);
    }
}
