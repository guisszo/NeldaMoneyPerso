<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="transactions")
     * @Groups({"transactionEnv"})
     */
    private $userEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="transactions")
     * @Groups({"CodeTransaction"})
     */
    private $userRetrait;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"transactionEnv","CodeTransaction"})
     */
    private $nomcompletExpediteur;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups({"transactionEnv","CodeTransaction"})
     */
    private $telExpediteur;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"transactionEnv","CodeTransaction"})
     */
    private $nomcompletRecepteur;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Groups({"transactionEnv"})
     */
    private $telRecepteur;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"transactionEnv","CodeTransaction"})
     */
    private $codeTransaction;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transactionEnv","CodeTransaction"})
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     * Groups({"CodeTransaction"})
     */
    private $CNIrecepteur;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $SentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recevedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commissionEnv;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commissionRetrait;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commissionEtat;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commissionNeldam;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserEnvoi(): ?Utilisateur
    {
        return $this->userEnvoi;
    }

    public function setUserEnvoi(?Utilisateur $userEnvoi): self
    {
        $this->userEnvoi = $userEnvoi;

        return $this;
    }

    public function getUserRetrait(): ?Utilisateur
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?Utilisateur $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getNomcompletExpediteur(): ?string
    {
        return $this->nomcompletExpediteur;
    }

    public function setNomcompletExpediteur(string $nomcompletExpediteur): self
    {
        $this->nomcompletExpediteur = $nomcompletExpediteur;

        return $this;
    }

    public function getTelExpediteur(): ?string
    {
        return $this->telExpediteur;
    }

    public function setTelExpediteur(string $telExpediteur): self
    {
        $this->telExpediteur = $telExpediteur;

        return $this;
    }

    public function getNomcompletRecepteur(): ?string
    {
        return $this->nomcompletRecepteur;
    }

    public function setNomcompletRecepteur(string $nomcompletRecepteur): self
    {
        $this->nomcompletRecepteur = $nomcompletRecepteur;

        return $this;
    }

    public function getTelRecepteur(): ?string
    {
        return $this->telRecepteur;
    }

    public function setTelRecepteur(string $telRecepteur): self
    {
        $this->telRecepteur = $telRecepteur;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCNIrecepteur(): ?string
    {
        return $this->CNIrecepteur;
    }

    public function setCNIrecepteur(?string $CNIrecepteur): self
    {
        $this->CNIrecepteur = $CNIrecepteur;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getRecevedAt(): ?\DateTimeInterface
    {
        return $this->recevedAt;
    }

    public function setRecevedAt(?\DateTimeInterface $recevedAt): self
    {
        $this->recevedAt = $recevedAt;

        return $this;
    }

    public function getCommissionEnv(): ?int
    {
        return $this->commissionEnv;
    }

    public function setCommissionEnv(?int $commissionEnv): self
    {
        $this->commissionEnv = $commissionEnv;

        return $this;
    }

    public function getCommissionRetrait(): ?int
    {
        return $this->commissionRetrait;
    }

    public function setCommissionRetrait(?int $commissionRetrait): self
    {
        $this->commissionRetrait = $commissionRetrait;

        return $this;
    }

    public function getCommissionEtat(): ?int
    {
        return $this->commissionEtat;
    }

    public function setCommissionEtat(?int $commissionEtat): self
    {
        $this->commissionEtat = $commissionEtat;

        return $this;
    }

    public function getCommissionNeldam(): ?int
    {
        return $this->commissionNeldam;
    }

    public function setCommissionNeldam(?int $commissionNeldam): self
    {
        $this->commissionNeldam = $commissionNeldam;

        return $this;
    }

    /**
     * Get the value of SentAt
     */ 
    public function getSentAt()
    {
        return $this->SentAt;
    }

    /**
     * Set the value of SentAt
     *
     * @return  self
     */ 
    public function setSentAt($SentAt)
    {
        $this->SentAt = $SentAt;

        return $this;
    }
}
