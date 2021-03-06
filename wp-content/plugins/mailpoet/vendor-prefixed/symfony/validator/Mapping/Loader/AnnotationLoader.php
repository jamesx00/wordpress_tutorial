<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Constraints\Callback;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequenceProvider;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
class AnnotationLoader implements LoaderInterface
{
 protected $reader;
 public function __construct(Reader $reader)
 {
 $this->reader = $reader;
 }
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 $reflClass = $metadata->getReflectionClass();
 $className = $reflClass->name;
 $success = \false;
 foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
 if ($constraint instanceof GroupSequence) {
 $metadata->setGroupSequence($constraint->groups);
 } elseif ($constraint instanceof GroupSequenceProvider) {
 $metadata->setGroupSequenceProvider(\true);
 } elseif ($constraint instanceof Constraint) {
 $metadata->addConstraint($constraint);
 }
 $success = \true;
 }
 foreach ($reflClass->getProperties() as $property) {
 if ($property->getDeclaringClass()->name === $className) {
 foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
 if ($constraint instanceof Constraint) {
 $metadata->addPropertyConstraint($property->name, $constraint);
 }
 $success = \true;
 }
 }
 }
 foreach ($reflClass->getMethods() as $method) {
 if ($method->getDeclaringClass()->name === $className) {
 foreach ($this->reader->getMethodAnnotations($method) as $constraint) {
 if ($constraint instanceof Callback) {
 $constraint->callback = $method->getName();
 $metadata->addConstraint($constraint);
 } elseif ($constraint instanceof Constraint) {
 if (\preg_match('/^(get|is|has)(.+)$/i', $method->name, $matches)) {
 $metadata->addGetterMethodConstraint(\lcfirst($matches[2]), $matches[0], $constraint);
 } else {
 throw new MappingException(\sprintf('The constraint on "%s::%s()" cannot be added. Constraints can only be added on methods beginning with "get", "is" or "has".', $className, $method->name));
 }
 }
 $success = \true;
 }
 }
 }
 return $success;
 }
}
