enum OvoIssueCode {
  custom,
  invalidType,
  invalidLiteral,
  invalidUnion,
  invalidUnionDiscriminator,
  invalidEnumValue,
  invalidArguments,

  unrecognizedType,
}

class OvoIssue {
  final OvoIssueCode code;
  final bool? fatal;
  final String? message;

  const OvoIssue(this.code, {this.fatal, this.message});
}

class OvoException<T> implements Exception {
  final List<OvoIssue> issues;

  const OvoException(this.issues);
}

class OvoExceptionContext<T> {
  final T data;
  final String message;

  const OvoExceptionContext(this.data, this.message);
}

typedef OvoMessageMapper = String Function(
    OvoIssueCode code, OvoExceptionContext context);
