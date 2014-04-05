<?php


namespace Floppy\Server\RequestHandler\Security;


use Floppy\Common\FileId;
use Floppy\Common\FileSource;
use Floppy\Server\FileHandler\FileHandlerProvider;
use Floppy\Server\RequestHandler\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Floppy\Common\ChecksumChecker;
use Floppy\Server\RequestHandler\AccessDeniedException;

class PolicyRule implements Rule
{
    private $checksumChecker;
    private $fileHandlerProvider;

    public function __construct(ChecksumChecker $checksumChecker, array $fileHandlers)
    {
        $this->checksumChecker = $checksumChecker;
        $this->fileHandlerProvider = new FileHandlerProvider($fileHandlers);
    }

    public function checkRule(Request $request, $object = null)
    {
        $policy = $this->retrievePolicy($request);
        $this->checkExpiration($policy);
        $this->checkFileType($policy, $object);
    }

    protected function retrievePolicy(Request $request)
    {
        $policy = $request->request->get('policy');
        $signature = $request->request->get('signature');

        if (!$policy || !$signature || !$this->checksumChecker->isChecksumValid($signature, $policy)) {
            throw new AccessDeniedException();
        }

        $decodedPolicy = @json_decode(base64_decode($policy), true);

        if ($decodedPolicy === false) {
            throw new AccessDeniedException('Invalid policy format, deserialization failed');
        }

        return $decodedPolicy;
    }

    protected function checkExpiration($policy)
    {
        if (!empty($policy['expiration']) && $policy['expiration'] < time()) {
            throw new AccessDeniedException('Policy is expired');
        }
    }

    protected function checkFileType($policy, $object)
    {
        if($object instanceof FileSource && !empty($policy['file_types'])) {
            $fileHandlerName = $this->fileHandlerProvider->findFileHandlerName($object);
            $fileTypes = (array) $policy['file_types'];

            if(!in_array($fileHandlerName, $fileTypes)) {
                throw new BadRequestException(sprintf('Invalid file type, given "%s", allowed %s', $fileHandlerName, implode(', ', $fileTypes)));
            }
        }
    }
}