<?php
namespace Cmobi\RabbitmqBundle\Domain;

trait TimestampableTrait
{
    /**
     * @var \DateTime
     *
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     */
    private $updatedAt;

    /**
     * Set createdAt
     *
     * @param $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get CreatedAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set UpdatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    /**
     * Get UpdateAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime("now"));
    }

    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }
}