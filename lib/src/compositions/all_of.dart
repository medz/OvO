import '../core/context.dart';
import '../core/exception.dart';
import '../core/ovo.dart';

/// A schema that validates data against all of the provided OvO schemas.
///
/// Example:
/// ```dart
/// final allOf = AllOf([
///   const Number().min(5),
///   const Number().max(10),
/// ], 'The value must be a number between 5 and 10.');
/// ```
class AllOf implements OvO {
  /// An iterable of OvO schemas that need to be validated.
  final Iterable<OvO> schemas;

  /// The message that will be thrown when validation fails.
  final String? message;

  /// Creates a new instance of [AllOf].
  ///
  /// The [schemas] parameter is an iterable of OvO schemas that need to be validated.
  /// The [message] parameter is the message that will be thrown when validation fails.
  const AllOf(this.schemas, [this.message]);

  @override
  Future handle(Context context, data) async {
    final exceptions = <Exception>[];
    for (final schema in schemas) {
      try {
        await schema.handle(context, data);
      } on Exception catch (e) {
        exceptions.add(e);
      }
    }

    if (exceptions.isEmpty) return data;
    throw OvOException(
      code: #all_of,
      message: message ?? 'All schemas must be valid',
      exceptions: exceptions,
    );
  }
}
