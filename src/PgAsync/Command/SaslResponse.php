<?php

declare(strict_types=1);

namespace PgAsync\Command;

use PgAsync\ScramSha256;

class SaslResponse implements CommandInterface
{
    use CommandTrait;

    /**
     * @var ScramSha256
     */
    private $scramSha265;

    /**
     * @param ScramSha256 $scramSha265
     */
    public function __construct(ScramSha256 $scramSha265)
    {
        $this->scramSha265 = $scramSha265;
    }

    /**
     * @return string
     */
    public function encodedMessage(): string
    {
        $clientFinalMessage = $this->createClientFinalMessage();
        $messageLength = strlen($clientFinalMessage) + 4;

        return 'p' . pack('N', $messageLength) . $clientFinalMessage;
    }

    /**
     * @return bool
     */
    public function shouldWaitForComplete(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    private function createClientFinalMessage(): string
    {
        return $this->scramSha265->getClientFirstMessageWithoutProof() . ',p=' . base64_encode($this->scramSha265->getClientProof());
    }
}